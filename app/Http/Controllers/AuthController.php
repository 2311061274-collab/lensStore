<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;
            if ($role === 'admin' || $role === 'staff') {
                return redirect()->route('admin.dashboard');
            }
            // Nếu chưa xác thực email, gửi OTP và chuyển về trang nhập mã
            if (! Auth::user()->hasVerifiedEmail()) {
                $this->generateAndSendOtp($request, Auth::user()->email, Auth::user()->name);

                return redirect()->route('verification.notice');
            }

            return redirect()->route('storefront.index');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ]);
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Rate limit đăng ký chống spam/bot tạo tài khoản hàng loạt
        $ip = $request->ip();
        $rateKey = 'register_attempts:'.$ip;
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            $seconds = RateLimiter::availableIn($rateKey);

            return back()->withErrors(['email' => "Quá nhiều yêu cầu đăng ký từ địa chỉ của bạn. Vui lòng thử lại sau {$seconds} giây."]);
        }
        RateLimiter::hit($rateKey, 3600); // 10 attempts per hour

        // Validate dữ liệu - Không yêu cầu CCCD/sinh trắc học cho đăng ký thông thường
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'birthday' => 'nullable|date|before:-16 years',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng trên hệ thống.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.unique' => 'Số điện thoại này đã được đăng ký.',
            'birthday.before' => 'Bạn phải từ 16 tuổi trở lên.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Lưu thông tin đăng ký vào session (chưa tạo user vào DB)
        $request->session()->put('pending_registration', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'phone' => $validated['phone'],
            'cccd' => null, // Xác nhận rõ không thu thập CCCD theo quy định Master Prompt
            'birthday' => $validated['birthday'] ?? null,
            'gender' => $validated['gender'] ?? 'male',
            'address' => $validated['address'] ?? null,
        ]);

        // Sinh OTP, băm bảo mật và gửi email
        $sent = $this->generateAndSendOtp($request, $validated['email'], $validated['name']);
        if (! $sent['success']) {
            return back()->withErrors(['email' => $sent['message']])->withInput();
        }

        return redirect()->route('verification.notice')
            ->with('success', 'Mã xác thực OTP đã được gửi về email của bạn. Vui lòng kiểm tra hộp thư.');
    }

    // Hiển thị trang nhập OTP
    public function showVerifyOtpForm(Request $request)
    {
        // Nếu không có pending_registration và cũng chưa login → về trang đăng ký
        if (! $request->session()->has('pending_registration') && ! Auth::check()) {
            return redirect()->route('register');
        }

        return view('auth.verify-email');
    }

    // Xử lý xác thực OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Vui lòng nhập mã xác thực.',
            'otp.size' => 'Mã xác thực phải có đúng 6 số.',
        ]);

        // Lấy thông tin OTP từ session
        $storedOtpHash = $request->session()->get('otp_hash');
        $otpExpiry = $request->session()->get('otp_expires_at');
        $attempts = (int) $request->session()->get('otp_attempts', 0);

        if (! $storedOtpHash) {
            return back()->withErrors(['otp' => 'Phiên xác thực đã hết hạn hoặc không tồn tại. Vui lòng yêu cầu gửi lại mã mới.']);
        }

        // Kiểm tra thời hạn OTP
        if (Carbon::now()->isAfter($otpExpiry)) {
            $request->session()->forget(['otp_hash', 'otp_expires_at']);

            return back()->withErrors(['otp' => 'Mã xác thực đã hết hạn (quá 15 phút). Vui lòng nhấn gửi lại mã mới.']);
        }

        // Kiểm tra giới hạn số lần nhập sai (Chống Brute-force: tối đa 5 lần)
        if ($attempts >= 5) {
            $request->session()->forget(['otp_hash', 'otp_expires_at', 'otp_attempts']);

            return back()->withErrors(['otp' => 'Bạn đã nhập sai mã quá 5 lần. Mã này đã bị vô hiệu hóa vì lý do an toàn. Vui lòng yêu cầu gửi mã mới.']);
        }

        // Kiểm tra mã OTP qua Hash::check (Không bao giờ lưu plain text)
        if (! Hash::check($request->otp, $storedOtpHash)) {
            $attempts++;
            $request->session()->put('otp_attempts', $attempts);
            $remaining = 5 - $attempts;

            return back()->withErrors(['otp' => "Mã xác thực không chính xác. Bạn còn {$remaining} lần thử trước khi mã bị vô hiệu hóa."]);
        }

        // =========== CASE 1: ĐĂNG KÝ TÀI KHOẢN MỚI ===========
        $pendingData = $request->session()->get('pending_registration');

        if ($pendingData) {
            // Tạo user vào DB chỉ sau khi mã OTP hợp lệ
            $user = User::create(array_merge($pendingData, [
                'email_verified_at' => Carbon::now(),
            ]));

            // Xoá session OTP & pending
            $request->session()->forget([
                'pending_registration',
                'otp_hash',
                'otp_expires_at',
                'otp_email',
                'otp_attempts',
                'otp_last_sent_at',
            ]);

            Auth::login($user);

            return redirect()->route('storefront.index')
                ->with('success', 'Tài khoản của bạn đã được xác thực email thành công!');
        }

        // =========== CASE 2: User đã đăng nhập nhưng chưa verify email ===========
        $user = Auth::user();

        if ($user) {
            $user->update([
                'email_verified_at' => Carbon::now(),
                'verification_code' => null,
                'verification_code_expires_at' => null,
            ]);

            $request->session()->forget([
                'otp_hash',
                'otp_expires_at',
                'otp_email',
                'otp_attempts',
                'otp_last_sent_at',
            ]);

            return redirect()->route('storefront.index')
                ->with('success', 'Email đã được xác thực thành công!');
        }

        return redirect()->route('login');
    }

    // Gửi lại OTP với Cooldown chống spam (tối thiểu 60s)
    public function resendOtp(Request $request)
    {
        $lastSent = $request->session()->get('otp_last_sent_at');
        if ($lastSent && Carbon::now()->diffInSeconds(Carbon::parse($lastSent)) < 60) {
            $secondsLeft = 60 - Carbon::now()->diffInSeconds(Carbon::parse($lastSent));

            return back()->withErrors(['otp' => "Vui lòng chờ {$secondsLeft} giây trước khi yêu cầu gửi lại mã."]);
        }

        $pendingData = $request->session()->get('pending_registration');
        $otpEmail = $request->session()->get('otp_email');

        if ($pendingData) {
            $result = $this->generateAndSendOtp($request, $pendingData['email'], $pendingData['name']);
        } elseif (Auth::check()) {
            $user = Auth::user();
            $result = $this->generateAndSendOtp($request, $user->email, $user->name);
        } else {
            return redirect()->route('register');
        }

        if (! $result['success']) {
            return back()->withErrors(['otp' => $result['message']]);
        }

        return back()->with('message', 'Mã xác thực mới đã được gửi về email của bạn.');
    }

    // -------------------------------------------------------------------------
    // Helper: Sinh OTP 6 số, Hash bảo mật, lưu session, gửi email qua Gmail
    // -------------------------------------------------------------------------
    private function generateAndSendOtp(Request $request, string $email, string $name): array
    {
        // Cooldown check
        $lastSent = $request->session()->get('otp_last_sent_at');
        if ($lastSent && Carbon::now()->diffInSeconds(Carbon::parse($lastSent)) < 60) {
            $wait = 60 - Carbon::now()->diffInSeconds(Carbon::parse($lastSent));

            return ['success' => false, 'message' => "Vui lòng chờ {$wait} giây trước khi gửi lại OTP."];
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lưu HASH vào session (Tuyệt đối không lưu plain-text)
        $request->session()->put('otp_hash', Hash::make($otp));
        $request->session()->put('otp_expires_at', Carbon::now()->addMinutes(15)->toDateTimeString());
        $request->session()->put('otp_email', $email);
        $request->session()->put('otp_attempts', 0); // Reset số lần thử sai
        $request->session()->put('otp_last_sent_at', Carbon::now()->toDateTimeString());

        try {
            Mail::send('emails.otp-verification', [
                'user' => (object) ['name' => $name, 'email' => $email],
                'otp' => $otp,
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('🔐 Mã xác thực tài khoản LensStore của bạn');
            });

            return ['success' => true];
        } catch (\Throwable $e) {
            // Không log OTP hay mật khẩu
            Log::error('Gửi email OTP thất bại: '.$e->getMessage());

            return ['success' => false, 'message' => 'Không thể gửi email OTP vào lúc này. Vui lòng kiểm tra lại địa chỉ email hoặc thử lại sau ít phút.'];
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
