<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FramesCaptured extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $filenames;

    /**
     * Create a new event instance.
     *
     * @param $filenames
     */
    public function __construct($filenames)
    {
        $this->filenames = $filenames;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['test_channel'];
    }
}
