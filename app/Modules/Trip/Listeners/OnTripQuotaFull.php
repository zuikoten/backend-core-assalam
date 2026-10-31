<?php

namespace App\Modules\Trip\Listeners;

use App\Modules\Booking\Events\TripQuotaFull;
use App\Modules\Trip\Models\Trip;

class OnTripQuotaFull
{
    public function handle(TripQuotaFull $event): void
    {
        Trip::query()
            ->where('id', $event->tripId)
            ->update(['status' => 'full']);
    }
}
