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
            ->subject('Votre code de connexion')
            ->line('Voici le code à saisir pour vous connecter :')
            ->line($this->code)
            ->line('Si vous n’avez pas essayé de vous connecter à votre compte EK, vous pouvez ignorer ce message.');
    }
}