<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($galleryItem) {
            if (Storage::disk('public')->exists($galleryItem->image_path)) {
                Storage::disk('public')->delete($galleryItem->image_path);
            }
        });
    }
}
