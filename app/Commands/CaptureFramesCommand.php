<?php namespace App\Commands;

use Illuminate\Contracts\Queue\ShouldQueue;

class CaptureFramesCommand implements ShouldQueue
{

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $torrent;
    public $time;

    function __construct($torrent, $time, $amount)
    {
        $this->torrent = $torrent;
        $this->time = $time;
        $this->amount = $amount;
    }

}
