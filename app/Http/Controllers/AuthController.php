<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;
            if ($role === 'admin' || $role === 'staff') {
                return redirect()->route('admin.dashboard');
            }
            // Nếu chưa xác thực email, gửi OTP và chuyển về trang nhập mã
            if (!Auth::user()->hasVerifiedEmail()) {
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
        // Validate dữ liệu trước
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:20|unique:users,phone',
            'cccd'      => 'required|string|min:9|max:20|unique:users,cccd',
            'birthday'  => 'required|date|before:-18 years',
            'gender'    => 'required|in:male,female,other',
            'address'   => 'required|string|max:500',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
        ], [
            'name.required'      => 'Vui lòng nhập họ và tên.',
            'phone.required'     => 'Vui lòng nhập số điện thoại.',
            'phone.unique'       => 'Số điện thoại này đã được đăng ký.',
            'cccd.required'      => 'Vui lòng nhập số CCCD/CMND.',
            'cccd.min'           => 'Số CCCD/CMND phải có ít nhất 9 ký tự.',
            'cccd.unique'        => 'CCCD/CMND này đã được sử dụng.',
            'birthday.required'  => 'Vui lòng nhập ngày sinh.',
            'birthday.before'    => 'Bạn phải đủ 18 tuổi để đăng ký.',
            'gender.required'    => 'Vui lòng chọn giới tính.',
            'address.required'   => 'Vui lòng nhập địa chỉ.',
            'email.required'     => 'Vui lòng nhập địa chỉ email.',
            'email.unique'       => 'Email này đã được đăng ký.',
            'password.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // Lưu thông tin đăng ký vào session (chưa tạo user)
        $request->session()->put('pending_registration', [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'customer',
            'phone'    => $validated['phone'],
            'cccd'     => $validated['cccd'],
            'birthday' => $validated['birthday'],
            'gender'   => $validated['gender'],
            'address'  => $validated['address'],
        ]);

        // Sinh OTP và gửi email
        $this->generateAndSendOtp($request, $validated['email'], $validated['name']);

        return redirect()->route('verification.notice')
                         ->with('success', 'Mã xác thực đã được gửi về email của bạn. Vui lòng kiểm tra hộp thư.');
    }

    // Hiển thị trang nhập OTP
    public function showVerifyOtpForm(Request $request)
    {
        // Nếu không có pending_registration và cũng chưa login → về trang đăng ký
        if (!$request->session()->has('pending_registration') && !Auth::check()) {
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
            'otp.size'     => 'Mã xác thực phải có đúng 6 số.',
        ]);

        // Lấy OTP từ session
        $storedOtp     = $request->session()->get('otp_code');
        $otpExpiry     = $request->session()->get('otp_expires_at');
        $otpEmail      = $request->session()->get('otp_email');

        if (!$storedOtp) {
            return back()->withErrors(['otp' => 'Phiên xác thực đã hết hạn. Vui lòng thử lại.']);
        }

        // Kiểm tra hết hạn
        if (Carbon::now()->isAfter($otpExpiry)) {
            return back()->withErrors(['otp' => 'Mã xác thực đã hết hạn. Vui lòng yêu cầu gửi lại.']);
        }

        // Kiểm tra mã OTP
        if ($storedOtp !== $request->otp) {
            return back()->withErrors(['otp' => 'Mã xác thực không đúng. Vui lòng thử lại.']);
        }

        // =========== CASE 1: Đây là luồng ĐĂNG KÝ MỚI ===========
        $pendingData = $request->session()->get('pending_registration');

        if ($pendingData) {
            // Tạo user vào DB chỉ sau khi OTP đúng
            $user = User::create(array_merge($pendingData, [
                'email_verified_at' => Carbon::now(),
            ]));

            // Xoá session pending
            $request->session()->forget(['pending_registration', 'otp_code', 'otp_expires_at', 'otp_email']);

            Auth::login($user);

            return redirect()->route('storefront.index')
                             ->with('success', 'Tài khoản đã được tạo và xác thực thành công!');
        }

        // =========== CASE 2: User đã login nhưng chưa verified email ===========
        $user = Auth::user();

        if ($user) {
            $user->update([
                'email_verified_at'            => Carbon::now(),
                'verification_code'            => null,
                'verification_code_expires_at' => null,
            ]);

            $request->session()->forget(['otp_code', 'otp_expires_at', 'otp_email']);

            return redirect()->route('storefront.index')
                             ->with('success', 'Email đã được xác thực thành công!');
        }

        return redirect()->route('login');
    }

    // Gửi lại OTP
    public function resendOtp(Request $request)
    {
        $pendingData = $request->session()->get('pending_registration');
        $otpEmail    = $request->session()->get('otp_email');

        if ($pendingData) {
            $this->generateAndSendOtp($request, $pendingData['email'], $pendingData['name']);
        } elseif (Auth::check()) {
            $user = Auth::user();
            $this->generateAndSendOtp($request, $user->email, $user->name);
        } else {
            return redirect()->route('register');
        }

        return back()->with('message', 'Mã xác thực mới đã được gửi về email của bạn.');
    }

    // -------------------------------------------------------
    // Helper: Sinh OTP, lưu session, gửi email
    // -------------------------------------------------------
    private function generateAndSendOtp(Request $request, string $email, string $name): void
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $request->session()->put('otp_code',       $otp);
        $request->session()->put('otp_expires_at', Carbon::now()->addMinutes(15)->toDateTimeString());
        $request->session()->put('otp_email',      $email);

        Mail::send('emails.otp-verification', ['user' => (object)['name' => $name, 'email' => $email], 'otp' => $otp], function ($message) use ($email) {
            $message->to($email)
                    ->subject('🔐 Mã xác thực tài khoản LensStore của bạn');
        });
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
