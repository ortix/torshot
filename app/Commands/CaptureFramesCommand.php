<?php namespace App\Commands;

class CaptureFramesCommand
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
