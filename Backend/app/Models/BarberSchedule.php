<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id', 'weekday', 'start_time', 'end_time', 'break_start', 'break_end'
    ];

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}
