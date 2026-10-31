<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class TripCategory extends Model
{
    protected $table = 'trip_categories';

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

    public function services()
    {
        return $this->hasMany(TripCategoryService::class, 'trip_category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'trip_category_id');
    }
}
