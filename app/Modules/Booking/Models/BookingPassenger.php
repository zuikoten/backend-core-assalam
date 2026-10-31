<?php

namespace App\Modules\Booking\Models;

use App\Models\PilgrimProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPassenger extends Model
{
    protected $table = 'booking_passengers';

    protected $fillable = [
        'booking_id',
        'pilgrim_profile_id',
        'is_primary',
        'pricing_category_id',
        'full_name',
        'nik',
        'passport_number',
        'passport_expiry',
        'birth_date',
        'gender',
        'phone',
        'foto_ktp',
        'foto_passport',
        'price',
        'document_status',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'price' => 'decimal:2',
        'passport_expiry' => 'date',
        'birth_date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }


    public function pilgrimProfile(): BelongsTo
    {
        return $this->belongsTo(PilgrimProfile::class, 'pilgrim_profile_id');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByDocumentStatus($query, string $status)
    {
        return $query->where('document_status', $status);
    }
}
