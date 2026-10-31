<?php

namespace App\Modules\Trip\Observers;

use App\Modules\Trip\Events\TripBatchStatusChanged;
use App\Modules\Trip\Models\TripBatch;

class TripBatchObserver
{
    public function updated(TripBatch $batch): void
    {
        if (!$batch->wasChanged('status')) {
            return;
        }

        event(new TripBatchStatusChanged(
            batch: $batch,
            oldStatus: (string) $batch->getOriginal('status'),
            newStatus: (string) $batch->status,
        ));
    }
}
