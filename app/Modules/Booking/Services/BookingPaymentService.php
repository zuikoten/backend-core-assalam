<?php

namespace App\Modules\Booking\Services;

use App\Modules\Booking\Events\BookingConfirmed;
use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Models\BookingPayment;
use Illuminate\Database\Transaction\Closure;
use Illuminate\Support\Facades\DB;

class BookingPaymentService
{
    public function confirmPayment(int $bookingId, array $payload): Booking
    {
        return DB::transaction(function () use ($bookingId, $payload) {
            /** @var Booking $booking */
            $booking = Booking::query()->findOrFail($bookingId);

            /** @var BookingPayment $payment */
            $payment = BookingPayment::query()->where('booking_id', $bookingId)->firstOrFail();

            $payment->update([
                'status' => 'success',
                'paid_at' => now(),
                'confirmed_by' => $payload['confirmed_by'] ?? null,
                'confirmed_at' => now(),
            ]);

            $booking->update(['status' => 'confirmed']);

            event(new BookingConfirmed($booking));

            return $booking;
        });
    }
}
