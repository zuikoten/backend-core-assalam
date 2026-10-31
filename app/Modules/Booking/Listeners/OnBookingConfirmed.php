<?php

namespace App\Modules\Booking\Listeners;

use App\Modules\Booking\Events\BookingConfirmed;
use App\Modules\Booking\Events\TripBatchQuotaFull;
use App\Modules\Booking\Events\TripQuotaFull;
use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Models\BookingPayment;
use App\Modules\Booking\Services\KuotaCheckerService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;

class OnBookingConfirmed
{
    public function __construct(private KuotaCheckerService $kuotaCheckerService) {}

    public function handle(BookingConfirmed $event): void
    {
        $booking = $event->booking;

        $totalBooked = $booking->trip_batch_id
            ? Booking::query()
            ->where('trip_batch_id', $booking->trip_batch_id)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_pax')
            : Booking::query()
            ->where('trip_id', $booking->trip_id)
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_pax');

        $remaining = $booking->trip_batch_id
            ? $this->kuotaCheckerService->getRemainingQuota(null, (int) $booking->trip_batch_id)
            : $this->kuotaCheckerService->getRemainingQuota((int) $booking->trip_id, null);

        // remaining < 0 berarti overbook (seharusnya tidak terjadi), tapi kita treat sebagai full
        if ($remaining <= 0) {
            if ($booking->trip_batch_id) {
                event(new TripBatchQuotaFull((int) $booking->trip_batch_id));
            } else {
                event(new TripQuotaFull((int) $booking->trip_id));
            }
        }
    }
}
