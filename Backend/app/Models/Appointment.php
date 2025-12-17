<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_code',
        'client_name',
        'client_phone',
        'client_email',
        'barber_id',
        'service_id',
        'starts_at',
        'ends_at',
        'notes',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }

    public static function generatePublicCode(): string
    {
        do {
            $code = 'APT-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('public_code', $code)->exists());

        return $code;
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['PENDING', 'CONFIRMED']);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get appointment end time based on service duration
     */
    public function getEndTimeAttribute(): string
    {
        $timeCarbon = \Carbon\Carbon::createFromFormat('H:i:s', $this->time);
        $duration = $this->service->duration_minutes;
        return $timeCarbon->addMinutes($duration)->format('H:i:s');
    }

    /**
     * Check if this appointment overlaps with given time range
     */
    public function overlapsWithTimeRange(string $startTime, string $endTime): bool
    {
        $appointmentStart = \Carbon\Carbon::createFromFormat('H:i:s', $this->time);
        $appointmentEnd = \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time);
        $rangeStart = \Carbon\Carbon::createFromFormat('H:i:s', $startTime);
        $rangeEnd = \Carbon\Carbon::createFromFormat('H:i:s', $endTime);

        return $appointmentStart->lt($rangeEnd) && $appointmentEnd->gt($rangeStart);
    }
}
