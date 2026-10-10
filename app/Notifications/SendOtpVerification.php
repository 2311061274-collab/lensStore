<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOtpVerification extends Notification
{
    use Queueable;

    public function __construct(public string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🔐 Mã xác thực tài khoản LensStore của bạn')
            ->view('emails.otp-verification', [
                'user' => $notifiable,
                'otp' => $this->otp,
            ]);
    }
}
