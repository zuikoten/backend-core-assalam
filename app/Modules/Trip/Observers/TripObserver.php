<?php

namespace App\Modules\Trip\Observers;

use App\Modules\Trip\Events\TripStatusChanged;
use App\Modules\Trip\Models\Trip;
use Illuminate\Support\Str;

class TripObserver
{
    public function creating(Trip $trip): void
    {
        if (empty($trip->slug) && !empty($trip->title)) {
            $trip->slug = Str::slug($trip->title) . '-' . substr(uniqid('', true), -4);
        }

        if (empty($trip->trip_code)) {
            $trip->trip_code = 'TRP-' . date('Y') . '-' . strtoupper(substr(uniqid('', true), -4));
        }
    }

    public function updated(Trip $trip): void
    {
        if (!$trip->wasChanged('status')) {
            return;
        }

        event(new TripStatusChanged(
            trip: $trip,
            oldStatus: (string) $trip->getOriginal('status'),
            newStatus: (string) $trip->status,
        ));
    }
}
