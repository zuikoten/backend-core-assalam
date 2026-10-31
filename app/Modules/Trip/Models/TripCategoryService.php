<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class TripCategoryService extends Model
{
    protected $table = 'trip_category_services';

    protected $fillable = [
        'trip_category_id',
        'name',
        'slug',
        'description',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(TripCategory::class, 'trip_category_id');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'trip_category_service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
