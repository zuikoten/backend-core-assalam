<?php

namespace App\Modules\Booking\Models;

use App\Models\User;
use App\Modules\AgentSystem\Models\Agent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $table = 'bookings';

    protected $fillable = [
        'booking_number',
        'trip_id',
        'trip_batch_id',
        'package_id',
        'user_id',
        'agent_id',
        'total_pax',
        'total_amount',
        'status',
        'document_status',
        'customer_notes',
        'admin_notes',
        'created_by',
    ];

    protected $casts = [
        'total_pax' => 'integer',
        'total_amount' => 'decimal:2',
        'trip_batch_id' => 'integer',
    ];

    public function passengers(): HasMany
    {
        return $this->hasMany(BookingPassenger::class, 'booking_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(BookingPayment::class, 'booking_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function scopeByTrip($query, ?int $tripId)
    {
        return $tripId === null ? $query : $query->where('trip_id', $tripId);
    }

    public function scopeByBatch($query, ?int $batchId)
    {
        return $batchId === null ? $query->whereNull('trip_batch_id') : $query->where('trip_batch_id', $batchId);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled']);
    }
}
