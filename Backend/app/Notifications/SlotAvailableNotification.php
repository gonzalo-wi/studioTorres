<?php

namespace App\Notifications;

use App\Models\Waitlist;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SlotAvailableNotification extends Notification
{
    use Queueable;

    protected $waitlistEntry;
    protected $availableSlot;

    /**
     * Create a new notification instance.
     */
    public function __construct(Waitlist $waitlistEntry, array $availableSlot)
    {
        $this->waitlistEntry = $waitlistEntry;
        $this->availableSlot = $availableSlot;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $date = $this->availableSlot['date'];
        $time = $this->availableSlot['time'];
        $serviceName = $this->waitlistEntry->service->title;
        $barberName = $this->waitlistEntry->barber 
            ? $this->waitlistEntry->barber->user->name 
            : 'Cualquier barbero';

        // URL para confirmar el turno (2 horas de ventana)
        $confirmUrl = config('app.frontend_url') . '/book/confirm-waitlist/' . $this->waitlistEntry->id;

        return (new MailMessage)
            ->subject('Turno Disponible - Studio Torres')
            ->view('emails.slot-available', [
                'clientName' => $this->waitlistEntry->client_name,
                'date' => $date->format('d/m/Y'),
                'time' => $time,
                'serviceName' => $serviceName,
                'barberName' => $barberName,
                'confirmUrl' => $confirmUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'waitlist_id' => $this->waitlistEntry->id,
            'slot' => $this->availableSlot,
        ];
    }
}
