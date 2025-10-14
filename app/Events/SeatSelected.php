<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatSelected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $seatIds;
    public $showtimeId;

    public function __construct(array $seatIds, $showtimeId)
    {
        $this->seatIds = $seatIds;
        $this->showtimeId = $showtimeId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('showtime.'.$this->showtimeId);
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'SeatSelected';
    }
}
