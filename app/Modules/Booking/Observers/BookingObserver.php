<?php

namespace App\Modules\Booking\Observers;

use App\Modules\Booking\Models\Booking;

class BookingObserver
{
    public function creating(Booking $booking): void
    {
        $booking->booking_number = 'BK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    }
}
