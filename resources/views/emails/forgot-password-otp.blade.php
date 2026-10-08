<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
    .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
    .header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee; }
    .header h1 { margin: 0; color: #4F46E5; font-size: 24px; }
    .content { padding: 20px 0; }
    .otp-code { text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #4F46E5; margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 8px; }
    .footer { text-align: center; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #888; }
</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LensStore</h1>
        </div>
        <div class="content">
            <p>Chào {{ $user->name }},</p>
            <p>Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản liên kết với email này. Vui lòng sử dụng mã xác thực dưới đây để tiếp tục:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p>Mã này sẽ hết hạn sau <strong>15 phút</strong>.</p>
            <p>Nếu bạn không yêu cầu khôi phục mật khẩu, vui lòng bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} LensStore. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
