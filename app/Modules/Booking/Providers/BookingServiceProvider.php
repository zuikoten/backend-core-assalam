<?php

namespace App\Modules\Booking\Providers;

use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Models\BookingPassenger;
use App\Modules\Booking\Observers\BookingObserver;
use App\Modules\Booking\Observers\BookingPassengerObserver;
use App\Modules\Booking\Events\BookingConfirmed;
use App\Modules\Booking\Listeners\OnBookingConfirmed;
use App\Modules\Booking\Listeners\OnTripBatchStatusChanged;
use App\Modules\Booking\Listeners\OnTripStatusChanged;
use Illuminate\Support\ServiceProvider;

class BookingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Booking::observe(BookingObserver::class);
        BookingPassenger::observe(BookingPassengerObserver::class);

        \Illuminate\Support\Facades\Event::listen(BookingConfirmed::class, [
            OnBookingConfirmed::class,
        ]);

        // Listener untuk event dari Modul Trip (kelas event Trip mungkin belum ada di repo ini)
        if (class_exists('App\\Modules\\Trip\\Events\\TripStatusChanged')) {
            \Illuminate\Support\Facades\Event::listen(
                \App\Modules\Trip\Events\TripStatusChanged::class,
                [OnTripStatusChanged::class]
            );
        }

        if (class_exists('App\\Modules\\Trip\\Events\\TripBatchStatusChanged')) {
            \Illuminate\Support\Facades\Event::listen(
                \App\Modules\Trip\Events\TripBatchStatusChanged::class,
                [OnTripBatchStatusChanged::class]
            );
        }
    }

    public function register(): void
    {
        // no-op
    }
}
