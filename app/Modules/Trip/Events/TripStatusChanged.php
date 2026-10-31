<?php

namespace App\Modules\Trip\Events;

use App\Modules\Trip\Models\Trip;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Trip $trip,
        public string $oldStatus,
        public string $newStatus,
    ) {}
}
