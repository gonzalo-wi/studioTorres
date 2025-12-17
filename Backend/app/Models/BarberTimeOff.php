<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarberTimeOff extends Model
{
    use HasFactory;

    protected $table = 'barber_time_off';

    protected $fillable = [
        'barber_id',
        'starts_at',
        'ends_at',
        'reason',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Get the barber that owns this time-off period
     */
    public function barber(): BelongsTo
    {
        return $this->belongsTo(Barber::class);
    }
}
