<?php

namespace App\Modules\Trip\Events;

use App\Modules\Trip\Models\TripBatch;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripBatchStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TripBatch $batch,
        public string $oldStatus,
        public string $newStatus,
    ) {}
}
