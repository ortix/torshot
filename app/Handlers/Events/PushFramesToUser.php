<?php

namespace App\Handlers\Events;

use App\Events\FramesCaptured;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PushFramesToUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FramesCaptured  $event
     * @return void
     */
    public function handle(FramesCaptured $event)
    {

    }
}
