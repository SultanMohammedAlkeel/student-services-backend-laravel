<?php

namespace App\Mail;

use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $resetCode;
    public $user;
    public $appName;
    public $universityName;

    /**
     * Create a new message instance.
     */
    public function __construct(PasswordResetCode $resetCode, User $user)
    {
        $this->resetCode = $resetCode;
        $this->user = $user;
        $this->appName = config("app.name", "يوتا - الخدمات الطلابية");
        $this->universityName = "جامعة ذمار - كلية الحاسبات والمعلوماتية";
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "رمز إعادة تعيين كلمة المرور - " . $this->appName,
            // تم إزالة from و replyTo هنا للاعتماد على الإعدادات العامة في .env و config/mail.php
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: "emails.passwordresetcode", // تم تغيير html و text إلى view
            with: [
                "resetCode" => $this->resetCode,
                "user" => $this->user,
                "appName" => $this->appName,
                "universityName" => $this->universityName,
                "expiryMinutes" => 3,
                "supportEmail" => config("mail.support.address", "yottatofttupportteam@gmail.com"),
                "appUrl" => config("app.url", "https://yotta-app.com"),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}


