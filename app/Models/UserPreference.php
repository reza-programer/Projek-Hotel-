<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'welcome_tea',
        'yukata_size',
        'pillow_type',
        'food_allergies',
        'breakfast_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
