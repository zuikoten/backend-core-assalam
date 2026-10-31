<?php

namespace App\Modules\Booking\Services;

use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Models\BookingPassenger;
use Illuminate\Database\Transaction\Closure;
use Illuminate\Support\Facades\DB;
use App\Models\PilgrimProfile;

class BookingService
{
    public function __construct(private KuotaCheckerService $kuotaCheckerService) {}

    //@param array $passengersPayload array of passengers. For is_primary passenger should include pilgrim_profile_id.

    public function createBooking(array $payload): Booking
    {
        return DB::transaction(function () use ($payload) {
            $tripId = $payload['trip_id'];
            $tripBatchId = $payload['trip_batch_id'] ?? null;
            $totalPax = (int) $payload['total_pax'];

            // quota check
            $remaining = $tripBatchId
                ? $this->kuotaCheckerService->getRemainingQuota(null, (int) $tripBatchId)
                : $this->kuotaCheckerService->getRemainingQuota((int) $tripId, null);

            if ($remaining < $totalPax) {
                // The quota checker currently depends on quota_total existing in Trip module.
                // For now, keep logic per document contract: remaining must be >= pax.
                throw new \RuntimeException('Kuota trip tidak cukup');
            }

            $booking = Booking::create([
                'trip_id' => (int) $tripId,
                'trip_batch_id' => $tripBatchId ? (int) $tripBatchId : null,
                'package_id' => (int) $payload['package_id'],
                'user_id' => (int) $payload['user_id'],
                'agent_id' => $payload['agent_id'] ? (int) $payload['agent_id'] : null,
                'created_by' => $payload['created_by'] ?? null,
                'total_pax' => (int) $payload['total_pax'],
                'total_amount' => (string) $payload['total_amount'],
                'status' => 'pending',
                'document_status' => 'incomplete',
                'customer_notes' => $payload['customer_notes'] ?? null,
                'admin_notes' => $payload['admin_notes'] ?? null,
            ]);

            $passengers = $payload['passengers'] ?? [];
            foreach ($passengers as $p) {
                $isPrimary = (bool) ($p['is_primary'] ?? false);

                BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'pilgrim_profile_id' => $isPrimary ? ($p['pilgrim_profile_id'] ?? null) : ($p['pilgrim_profile_id'] ?? null),
                    'is_primary' => $isPrimary,
                    'pricing_category_id' => (int) $p['pricing_category_id'],
                    'full_name' => $p['full_name'],
                    'nik' => $p['nik'] ?? null,
                    'passport_number' => $p['passport_number'] ?? null,
                    'passport_expiry' => $p['passport_expiry'] ?? null,
                    'birth_date' => $p['birth_date'] ?? null,
                    'gender' => $p['gender'] ?? null,
                    'phone' => $p['phone'] ?? null,
                    'foto_ktp' => $p['foto_ktp'] ?? null,
                    'foto_passport' => $p['foto_passport'] ?? null,
                    'price' => (string) $p['price'],
                    'document_status' => $p['document_status'] ?? 'incomplete',
                ]);
            }

            return $booking;
        });
    }

    public function prefillPrimaryPassenger(int $userId): ?array
    {
        $pilgrimProfile = PilgrimProfile::where('user_id', $userId)->first();
        if (!$pilgrimProfile) {
            return null;
        }

        $user = $pilgrimProfile->user;

        return [
            'pilgrim_profile_id' => $pilgrimProfile->id,
            'full_name' => $user?->full_name ?? '',
            'nik' => $pilgrimProfile->nik,
            'passport_number' => $pilgrimProfile->passport_number,
            'passport_expiry' => $pilgrimProfile->passport_expiry,
            'foto_ktp' => $pilgrimProfile->foto_ktp,
            'foto_passport' => $pilgrimProfile->foto_passport,
        ];
    }
}
