<?php

namespace App\Modules\Trip\Listeners;

use App\Modules\Booking\Events\TripBatchQuotaFull;
use App\Modules\Trip\Models\TripBatch;

class OnTripBatchQuotaFull
{
    public function handle(TripBatchQuotaFull $event): void
    {
        TripBatch::query()
            ->where('id', $event->tripBatchId)
            ->update(['status' => 'full']);
    }
}
