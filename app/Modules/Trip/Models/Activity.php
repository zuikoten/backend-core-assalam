<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';

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
        return $this->belongsToMany(Trip::class, 'trip_activity', 'activity_id', 'trip_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
