<?php

namespace App\Modules\Booking\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPayment extends Model
{
    protected $table = 'booking_payments';

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'bank_name',
        'account_name',
        'transaction_ref',
        'status',
        'proof_of_payment',
        'paid_at',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
