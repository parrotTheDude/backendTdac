<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionNotification extends Notification
{
    use Queueable;

    private $listName;
    private $action;

    public function __construct($listName, $action)
    {
        $this->listName = $listName;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line("You have been {$this->action} the {$this->listName} mailing list.")
                    ->action('View Subscription', url('/'))
                    ->line('Thank you for your subscription action!');
    }
}
