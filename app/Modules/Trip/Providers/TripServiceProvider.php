<?php

namespace App\Modules\Trip\Providers;

use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripBatch;
use App\Modules\Trip\Observers\TripBatchObserver;
use App\Modules\Trip\Observers\TripObserver;
use App\Modules\Trip\Listeners\OnTripBatchQuotaFull;
use App\Modules\Trip\Listeners\OnTripQuotaFull;
use App\Modules\Booking\Events\TripBatchQuotaFull as BookingTripBatchQuotaFull;
use App\Modules\Booking\Events\TripQuotaFull as BookingTripQuotaFull;
use Illuminate\Support\ServiceProvider;

class TripServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // no-op
    }

    public function boot(): void
    {
        Trip::observe(TripObserver::class);
        TripBatch::observe(TripBatchObserver::class);

        // Event -> Listener mapping
        // NOTE: event-listener mapping bisa juga via EventServiceProvider.
        // Di provider ini kita daftarkan agar tetap berfungsi tanpa hunting file lain.
        $this->app->make('events')->listen(BookingTripQuotaFull::class, [OnTripQuotaFull::class, 'handle']);
        $this->app->make('events')->listen(BookingTripBatchQuotaFull::class, [OnTripBatchQuotaFull::class, 'handle']);
    }
}
