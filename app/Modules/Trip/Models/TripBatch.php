<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;

class TripBatch extends Model
{
    protected $table = 'trip_batches';

    protected $fillable = [
        'trip_id',
        'batch_name',
        'departure_date',
        'return_date',
        'quota_total',
        'team_leader_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function teamLeader()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'team_leader_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
