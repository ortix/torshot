<?php

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class SingleFrameCaptured extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $filename;

    /**
     * Create a new event instance.
     *
     * @param $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function broadcastWith()
    {
        $filename = str_ireplace(public_path(), '', $this->filename);
        return ['filename' => $filename];
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
