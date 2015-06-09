<?php

namespace App\Handlers\Events;

use App\Events\SingleFrameCaptured;

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
     * @param SingleFrameCaptured $event
     */
    public function handle(SingleFrameCaptured $event)
    {

    }
}
