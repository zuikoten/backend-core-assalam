<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class TripItinerary extends Model
{
    protected $table = 'trip_itineraries';

    protected $fillable = [
        'trip_id',
        'day_label',
        'title',
        'description',
        'sort_order',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}
