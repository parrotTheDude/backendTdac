<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Postmark\PostmarkClient;

class ResetPasswordNotification extends Notification
{
    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];  // Laravel triggers toMail method correctly
    }

    public function toMail($notifiable)
    {
        $resetUrl = URL::to('/reset-password/' . $this->token . '?email=' . urlencode($notifiable->email));

        $client = new PostmarkClient(config('services.postmark.token'));

        $toEmail = $notifiable->email;
        $templateId = 'password-reset';
        $templateModel = [
            'email' => $toEmail,
            'resetUrl' => $resetUrl
        ];
        $tag = "reset-password";
        $trackOpens = true;
        $trackLinks = "None";
        $messageStream = "admin";

        try {
            $sendResult = $client->sendEmailWithTemplate(
                config('services.postmark.from_email'),
                $toEmail,
                $templateId,
                $templateModel,
                $trackOpens,
                $tag,
                true,   // Inline CSS
                NULL,   // Reply To
                NULL,   // CC
                NULL,   // BCC
                NULL,   // Headers
                NULL,   // Attachments
                $trackLinks,
                NULL,   // Metadata
                $messageStream
            );

            \Log::info('Postmark Response:', (array)$sendResult);

        } catch (\Exception $e) {
            \Log::error('Postmark sending failed: ' . $e->getMessage());
        }

        // Properly return a dummy MailMessage to satisfy Laravel
        return (new MailMessage)
            ->subject('Password Reset Requested')
            ->line('We have sent a password reset link to your email.');
    }
}