<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WhatsAppNotification extends Notification
{
    use Queueable;

    protected string $messageBody;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $messageBody)
    {
        $this->messageBody = $messageBody;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // Placeholder for Twilio/Meta WhatsApp channel
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('WhatsApp Notification (Mockup)')
                    ->line($this->messageBody)
                    ->action('View Dashboard', url('/admin'))
                    ->line('Thank you for using our AI Agent!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_body' => $this->messageBody,
        ];
    }
}
