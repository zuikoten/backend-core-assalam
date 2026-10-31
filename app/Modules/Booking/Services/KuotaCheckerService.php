<?php

namespace App\Modules\Booking\Services;

use App\Modules\Booking\Models\Booking;
use Illuminate\Support\Facades\DB;

class KuotaCheckerService
{
    public function getRemainingQuota(?int $tripId, ?int $batchId = null): int
    {
        // remaining quota = quota_total - SUM(bookings.total_pax WHERE status != 'cancelled')
        if ($batchId !== null) {
            $quotaTotal = (int) DB::table('trip_batches')
                ->where('id', $batchId)
                ->value('quota_total');

            $totalBooked = Booking::query()
                ->where('trip_batch_id', $batchId)
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_pax');

            return $quotaTotal - (int) $totalBooked;
        }

        $quotaTotal = (int) DB::table('trips')
            ->where('id', $tripId)
            ->value('quota_total');

        $totalBooked = Booking::query()
            ->where('trip_id', $tripId)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_pax');

        return $quotaTotal - (int) $totalBooked;
    }
}
