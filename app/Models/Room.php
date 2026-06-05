<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name', 'name_jp', 'type', 'price', 'capacity', 'size', 'floor',
        'description', 'long_description', 'amenities', 'images', 'features',
        'color_theme', 'is_available', 'rating', 'review_count',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'features' => 'array',
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
