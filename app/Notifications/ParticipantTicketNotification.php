<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ParticipantTicketNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Event $event,
        private readonly Participant $participant,
        private readonly string $ticketUrl,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Event QR Ticket: ' . $this->event->title)
            ->greeting('Hello ' . ($this->participant->display_name ?: 'Participant') . ',')
            ->line('You are registered for: ' . $this->event->title)
            ->line('Invitation code: ' . $this->participant->invitation_code)
            ->line('Use the button below to open and print your ticket with QR code.')
            ->action('Open Ticket', $this->ticketUrl)
            ->line('One QR code allows one check-in entry only.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
