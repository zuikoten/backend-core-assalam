<?php

namespace App\Modules\Booking\Listeners;

use App\Modules\Booking\Events\BookingConfirmed;
use App\Modules\Booking\Models\Booking;

class OnTripStatusChanged
{
    public function handle(object $event): void
    {
        // Dokumen: mapping status
        // ongoing   -> departed
        // completed -> returned
        // cancelled -> cancelled (pending/confirmed)

        $tripId = $event->tripId ?? null;
        $status = $event->status ?? null;
        if (!$tripId || !$status) {
            return;
        }

        if ($status === 'ongoing') {
            Booking::query()
                ->where('trip_id', $tripId)
                ->where('status', 'confirmed')
                ->update(['status' => 'departed']);
        }

        if ($status === 'completed') {
            Booking::query()
                ->where('trip_id', $tripId)
                ->where('status', 'departed')
                ->update(['status' => 'returned']);
        }

        if ($status === 'cancelled') {
            Booking::query()
                ->where('trip_id', $tripId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->update(['status' => 'cancelled']);
        }
    }
}
