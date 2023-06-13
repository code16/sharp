<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Sharp2faDefaultNotification extends Notification implements ShouldQueue
{
    use \Illuminate\Bus\Queueable;

    public function __construct(public string $code)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('sharp::auth.2fa.notification.mail_subject'))
            ->line(trans('sharp::auth.2fa.notification.mail_body'))
            ->line($this->code);
    }
}