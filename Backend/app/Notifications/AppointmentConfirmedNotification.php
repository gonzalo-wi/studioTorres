<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmedNotification extends Notification
{
    use Queueable;

    protected $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
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
        $appointment = $this->appointment;
        $date = $appointment->starts_at->format('d/m/Y');
        $time = $appointment->starts_at->format('H:i');
        $serviceName = $appointment->service->title;
        $barberName = $appointment->barber->user->name;

        return (new MailMessage)
            ->subject('Turno Confirmado - Studio Torres')
            ->view('emails.appointment-confirmed', [
                'clientName' => $appointment->client_name,
                'date' => $date,
                'time' => $time,
                'serviceName' => $serviceName,
                'barberName' => $barberName,
                'appointmentCode' => $appointment->public_code,
                'detailsUrl' => config('app.frontend_url') . '/appointments/' . $appointment->public_code,
                'address' => config('app.barbershop_address', null),
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'public_code' => $this->appointment->public_code,
        ];
    }
}
