<?php

namespace App\Modules\Booking\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripBatchQuotaFull
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public int $tripBatchId) {}
}
