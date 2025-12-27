<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Waitlist extends Model
{
    use HasFactory;

    protected $table = 'waitlist';

    protected $fillable = [
        'client_name',
        'client_phone',
        'client_email',
        'service_id',
        'preferred_date',
        'preferred_time_start',
        'preferred_time_end',
        'barber_id',
        'status',
        'notified_at',
        'expires_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_time_start' => 'datetime',
        'preferred_time_end' => 'datetime',
        'notified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relación con el servicio
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relación con el barbero (opcional)
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Accessor para email (alias de client_email)
     */
    public function getEmailAttribute()
    {
        return $this->client_email;
    }

    /**
     * Scope: Solo entradas activas en espera
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'WAITING')
                     ->where('expires_at', '>', now());
    }

    /**
     * Scope: Filtra por fecha preferida
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('preferred_date', $date);
    }

    /**
     * Scope: Filtra por servicio
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Verifica si el horario dado cumple con las preferencias
     */
    public function matchesTimePreference(string $time): bool
    {
        // Si no especificó rango horario, acepta cualquier hora
        if (!$this->preferred_time_start && !$this->preferred_time_end) {
            return true;
        }

        $slotTime = Carbon::parse($time);

        if ($this->preferred_time_start && $this->preferred_time_end) {
            return $slotTime->between(
                Carbon::parse($this->preferred_time_start),
                Carbon::parse($this->preferred_time_end)
            );
        }

        if ($this->preferred_time_start) {
            return $slotTime->gte(Carbon::parse($this->preferred_time_start));
        }

        if ($this->preferred_time_end) {
            return $slotTime->lte(Carbon::parse($this->preferred_time_end));
        }

        return true;
    }

    /**
     * Marca como notificado
     */
    public function markAsNotified(): void
    {
        $this->update([
            'status' => 'NOTIFIED',
            'notified_at' => now(),
        ]);
    }

    /**
     * Marca como convertido en turno
     */
    public function markAsConverted(): void
    {
        $this->update(['status' => 'CONVERTED']);
    }

    /**
     * Marca como expirado
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'EXPIRED']);
    }

    /**
     * Verifica si ya expiró
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
