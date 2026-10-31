<?php

namespace App\Modules\Booking\Listeners;

use App\Modules\Booking\Models\Booking;

class OnTripBatchStatusChanged
{
    public function handle(object $event): void
    {
        $tripBatchId = $event->tripBatchId ?? null;
        $status = $event->status ?? null;
        if (!$tripBatchId || !$status) {
            return;
        }

        if ($status === 'ongoing') {
            Booking::query()
                ->where('trip_batch_id', $tripBatchId)
                ->where('status', 'confirmed')
                ->update(['status' => 'departed']);
        }

        if ($status === 'completed') {
            Booking::query()
                ->where('trip_batch_id', $tripBatchId)
                ->where('status', 'departed')
                ->update(['status' => 'returned']);
        }

        if ($status === 'cancelled') {
            Booking::query()
                ->where('trip_batch_id', $tripBatchId)
                ->whereIn('status', ['pending', 'confirmed'])
                ->update(['status' => 'cancelled']);
        }
    }
}
