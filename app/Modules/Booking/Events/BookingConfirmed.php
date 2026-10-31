<?php

namespace App\Modules\Booking\Events;

use App\Modules\Booking\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Booking $booking) {}
}
