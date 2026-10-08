<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class ForgotPasswordController extends Controller
{
    // Hiển thị form nhập email
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Xử lý gửi OTP
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email không tồn tại trong hệ thống.'
        ]);

        $user = User::where('email', $request->email)->first();

        // Sinh OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $request->session()->put('reset_password_otp', $otp);
        $request->session()->put('reset_password_email', $user->email);
        $request->session()->put('reset_password_expires_at', Carbon::now()->addMinutes(15));

        // Gửi email
        Mail::send('emails.forgot-password-otp', ['user' => $user, 'otp' => $otp], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('🔐 Mã xác thực khôi phục mật khẩu LensStore');
        });

        return redirect()->route('password.verify.form')
                         ->with('success', 'Mã xác thực khôi phục mật khẩu đã được gửi đến email của bạn.');
    }

    // Hiển thị form nhập OTP
    public function showVerifyOtpForm(Request $request)
    {
        if (!$request->session()->has('reset_password_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.passwords.verify-otp');
    }

    // Xử lý xác minh OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ], [
            'otp.required' => 'Vui lòng nhập mã xác thực.',
            'otp.size' => 'Mã xác thực phải có đúng 6 số.'
        ]);

        $storedOtp = $request->session()->get('reset_password_otp');
        $expiresAt = $request->session()->get('reset_password_expires_at');

        if (!$storedOtp) {
            return redirect()->route('password.request')->withErrors(['email' => 'Phiên làm việc đã hết hạn. Vui lòng yêu cầu lại.']);
        }

        if (Carbon::now()->isAfter($expiresAt)) {
            return back()->withErrors(['otp' => 'Mã xác thực đã hết hạn. Vui lòng yêu cầu gửi lại.']);
        }

        if ($storedOtp !== $request->otp) {
            return back()->withErrors(['otp' => 'Mã xác thực không đúng.']);
        }

        // OTP đúng -> Cho phép đổi mật khẩu
        $request->session()->put('reset_password_verified', true);
        
        return redirect()->route('password.reset.form');
    }

    // Hiển thị form đặt lại mật khẩu
    public function showResetForm(Request $request)
    {
        if (!$request->session()->get('reset_password_verified')) {
            return redirect()->route('password.request');
        }
        
        $email = $request->session()->get('reset_password_email');
        return view('auth.passwords.reset', compact('email'));
    }

    // Xử lý đổi mật khẩu
    public function resetPassword(Request $request)
    {
        if (!$request->session()->get('reset_password_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        $emailSession = $request->session()->get('reset_password_email');
        if ($emailSession !== $request->email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Có lỗi xảy ra, vui lòng thử lại.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa session
        $request->session()->forget(['reset_password_otp', 'reset_password_email', 'reset_password_expires_at', 'reset_password_verified']);

        return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập.');
    }
}
