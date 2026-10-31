<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $table = 'destinations';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_destination', 'destination_id', 'trip_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
