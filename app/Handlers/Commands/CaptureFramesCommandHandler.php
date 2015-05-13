<?php

namespace App\Handlers\Commands;


use App\Commands\CaptureFramesCommand;
use App\Services\FFMpegFrameCapture;
use App\Services\PeerflixStreamer;
use FFMpeg\Coordinate\TimeCode;
use Config;

class CaptureFramesCommandHandler
{
    private $capture;
    private $peerflix;


    /**
     * CaptureFramesHandler constructor.
     * @param FFMpegFrameCapture $capture
     * @param PeerflixStreamer $peerflix
     */
    public function __construct(FFMpegFrameCapture $capture, PeerflixStreamer $peerflix)
    {
        $this->capture = $capture;
        $this->peerflix = $peerflix;
    }

    public function handle(CaptureFramesCommand $command)
    {
        // Figure out the timecodes and return them here
        $timecodes = $this->capture->makeTimecodes($command->time, $command->amount);

        // Spin up peerflix in preparation for capturing the frames
        $this->peerflix
            ->torrent($command->torrent)
            ->hostname(\Config::get('torshot.peerflix.hostname'))
            ->port(\Config::get('torshot.peerflix.port'))
            ->run();

        // Extract the frames with ffmpeg
        $this->capture->setSource($this->peerflix->getServerLocation())
            ->setTimecodes($timecodes)
            ->extract(base_path() . '/tmp');

        // Kill peerflix when we're done
        $this->peerflix->kill();
    }

}