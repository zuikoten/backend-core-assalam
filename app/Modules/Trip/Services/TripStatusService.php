<?php

namespace App\Modules\Trip\Services;

use App\Modules\Trip\Models\Trip;
use Illuminate\Support\Arr;

class TripStatusService
{
    public function changeStatus(Trip $trip, string $newStatus): Trip
    {
        $allowed = ['draft', 'open', 'full', 'closed', 'ongoing', 'completed', 'cancelled'];
        if (!in_array($newStatus, $allowed, true)) {
            throw new \InvalidArgumentException('Status trip tidak valid');
        }

        $trip->status = $newStatus;
        $trip->save();

        return $trip;
    }
}
