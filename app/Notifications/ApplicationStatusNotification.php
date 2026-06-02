<?php

namespace App\Notifications;

use App\Models\RoomApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly RoomApplication $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Room Application Update')
            ->greeting('Hello '.$notifiable->name)
            ->line('Your dormitory room application is now '.$this->application->status.'.')
            ->line('Preferred room: '.($this->application->preferredRoom?->room_number ?? 'Any room'))
            ->line($this->application->admin_notes ?: 'Please check your student portal for details.')
            ->action('View Portal', route('student.dashboard'));
    }
}
