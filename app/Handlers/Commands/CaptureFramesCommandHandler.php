<?php

namespace App\Handlers\Commands;

use App\Commands\CaptureFramesCommand;
use app\Contracts\FrameCapture;
use App\Contracts\TorrentStreamer;
use App\Events\FramesCaptured;
use App\Services\FFMpegFrameCapture;
use App\Services\LocalStreamer;
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
     * @param PeerflixStreamer   $peerflix
     */
    public function __construct(FFMpegFrameCapture $capture, LocalStreamer $peerflix)
    {
        $this->capture = $capture;
        $this->peerflix = $peerflix;
    }

    public function handle(CaptureFramesCommand $command)
    {
        // Spin up peerflix in preparation for capturing the frames
        $this->peerflix
            ->torrent($command->torrent)
            ->hostname(\Config::get('torshot.peerflix.hostname'))
            ->port(\Config::get('torshot.peerflix.port'))
            ->run();

        // We need to set the source before we can do anything else
        $this->capture->setSource($this->peerflix->getServerLocation());

        // Figure out the timecodes and return them here.
        // As you can see the amount variable has precedence over the actual time
        if ($command->amount > 1) {
            $timecodes = $this->capture->timecodesFromAmount($command->amount);
        } else {
            $timecodes = $this->capture->timecodesFromTime($command->time);
        }

        // Extract the frames with ffmpeg
        $filenames = $this->capture->setTimecodes($timecodes)->extract(base_path() . '/tmp');

        // Kill peerflix when we're done
        $this->peerflix->kill();

        event(new FramesCaptured($filenames));

        return $filenames;
    }
}