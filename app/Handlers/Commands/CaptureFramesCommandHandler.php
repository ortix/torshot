<?php

namespace App\Handlers\Commands;

use App\Commands\CaptureFramesCommand;
use App\Services\FFMpegFrameCapture;
use App\Services\LocalStreamer;
use App\Services\PeerflixStreamer;
use Config;

class CaptureFramesCommandHandler
{
    private $capture;
    private $streamer;


    /**
     * CaptureFramesHandler constructor.
     * @param FFMpegFrameCapture $capture
     * @param PeerflixStreamer $streamer
     */
    public function __construct(FFMpegFrameCapture $capture, PeerflixStreamer $streamer)
    {
        $this->capture = $capture;
        $this->streamer = $streamer;
    }

    public function handle(CaptureFramesCommand $command)
    {
        // Spin up peerflix in preparation for capturing the frames
        $this->streamer
            ->torrent($command->torrent)
            ->hostname(\Config::get('torshot.peerflix.hostname'))
            ->port(\Config::get('torshot.peerflix.port'))
            ->run();

        // We need to set the source before we can do anything else
        $this->capture->setSource($this->streamer->getServerLocation());

        // Figure out the timecodes and return them here.
        // As you can see the amount variable has precedence over the actual time
        if ($command->amount > 1) {
            $timecodes = $this->capture->timecodesFromAmount($command->amount);
        } else {
            $timecodes = $this->capture->timecodesFromTime($command->time);
        }

        // Extract the frames with ffmpeg
        $filenames = $this->capture->setTimecodes($timecodes)->extract(public_path() . '/tmp');

        // Kill peerflix when we're done
        $this->streamer->kill();

        return $filenames;
    }
}