<?php

namespace App\Jobs;

use App\Contracts\FrameGrabber;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaptureFrame implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(FrameGrabber $grabber)
    {
        $grabber->setSource(\Config::get('torshot.test_video'));

        $timecodes = $grabber->timecodesFromAmount($this->request['amount']);

        // Extract the frames with ffmpeg
        $filenames = $grabber->setTimecodes($timecodes)->extract(base_path() . '/tmp');


    }
}
