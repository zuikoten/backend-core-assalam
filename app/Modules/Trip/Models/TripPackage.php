<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class TripPackage extends Model
{
    protected $table = 'trip_packages';

    protected $fillable = [
        'trip_id',
        'name',
        'room_type',
        'min_pax',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function prices()
    {
        return $this->hasMany(TripPackagePrice::class, 'trip_package_id');
    }
}
