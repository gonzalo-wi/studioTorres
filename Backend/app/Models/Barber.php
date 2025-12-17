<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'active', 'phone', 'email', 'avatar_url',
        'earnings_type', 'earnings_value'
    ];

    protected $casts = [
        'active' => 'boolean',
        'earnings_value' => 'decimal:2',
    ];

    public function schedules()
    {
        return $this->hasMany(BarberSchedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
