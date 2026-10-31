<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class Difficulty extends Model
{
    protected $table = 'difficulties';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_difficulty', 'difficulty_id', 'trip_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
