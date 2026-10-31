<?php

namespace App\Modules\Booking\Observers;

use App\Modules\Booking\Models\BookingPassenger;

class BookingPassengerObserver
{
    public function updated(BookingPassenger $passenger): void
    {
        $booking = $passenger->booking;
        if (!$booking) {
            return;
        }

        $allComplete = $booking->passengers
            ->every(fn($p) => $p->document_status !== 'incomplete');

        if ($allComplete) {
            $booking->update(['document_status' => 'complete']);
        }
    }
}
