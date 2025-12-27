<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'active', 'phone', 'email', 'avatar_url',
        'earnings_type', 'earnings_value', 'user_id'
    ];

    protected $casts = [
        'active' => 'boolean',
        'earnings_value' => 'decimal:2',
    ];

    protected $appends = ['avatar_full_url'];

    public function getAvatarFullUrlAttribute()
    {
        if (!$this->avatar_url) {
            return null;
        }
        
        // Si ya es una URL completa, retornarla
        if (str_starts_with($this->avatar_url, 'http')) {
            return $this->avatar_url;
        }
        
        // Construir URL completa
        return asset('storage/' . $this->avatar_url);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(BarberSchedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
