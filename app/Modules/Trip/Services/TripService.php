<?php

namespace App\Modules\Trip\Services;

use App\Modules\Trip\Models\Trip;

class TripService
{
    public function createTrip(array $payload): Trip
    {
        // Relasi banyak-ke-banyak disync dilakukan di sini bila payload memuat ids.
        $trip = Trip::create($payload);

        // Payload relasi (opsional)
        $trip->destinations()->sync($payload['destination_ids'] ?? []);
        $trip->activities()->sync($payload['activity_ids'] ?? []);
        $trip->difficulties()->sync($payload['difficulty_ids'] ?? []);

        return $trip;
    }

    public function updateTrip(Trip $trip, array $payload): Trip
    {
        $trip->update($payload);

        $trip->destinations()->sync($payload['destination_ids'] ?? []);
        $trip->activities()->sync($payload['activity_ids'] ?? []);
        $trip->difficulties()->sync($payload['difficulty_ids'] ?? []);

        return $trip;
    }
}
