<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name', 'origin', 'stay_type', 'rating', 'comment', 'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
