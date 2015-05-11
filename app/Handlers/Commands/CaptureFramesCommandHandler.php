<?php

namespace App\Handlers\Commands;


use App\Commands\CaptureFramesCommand;
use App\Services\FFMpegFrameCapture;
use App\Services\PeerflixStreamer;
use Config;

class CaptureFramesCommandHandler
{
    private $capture;
    private $peerflix;


    /**
     * CaptureFramesHandler constructor.
     */
    public function __construct(FFMpegFrameCapture $capture, PeerflixStreamer $peerflix)
    {
        $this->capture = $capture;
        $this->peerflix = $peerflix;
    }

    public function handle(CaptureFramesCommand $command)
    {
        // Figure out the timecodes and return them here
        // Start up peerflix with the torrent or magnet url
        // Pass the peerflix instance to FFMpeg and have it take screenshots
        $timecodes = $this->capture->makeTimecodes($command->time, $command->amount);

        // Spin up peerflix in preparation for capturing the frames
        $this->peerflix
            ->torrent($command->torrent)
            ->hostname(\Config::get('torshot.peerflix.hostname'))
            ->port(\Config::get('torshot.peerflix.port'))
            ->run();

        // Extract the frames with ffmpeg
        $this->capture->setSource($this->peerflix->getServerLocation())->setTime($timecodes);

        // Kill peerflix when we're done
        $this->peerflix->kill();
    }

    /**
     * Get the screenshot time from the input data in case it's set.
     * @param $data
     * @return string|boolean returns the screenshot time if set, otherwise false.
     */
    public function getFrameTime($data)
    {
        if (!empty($data['timecode'])) {
            return strtotime($data['timecode']) - strtotime('TODAY');
        }
        return false;
    }

    public function takeScreenshots($torrent, $time = null)
    {
        $pid = $this->startPeerflix($torrent);
        $timecodes = $this->getTimecodes($time);

        $filenames = []; // Create filenames array
        foreach ($timecodes as $key => $timecode) {
            $filenames[$key] = base_path() . '/tmp/' . '_' . $key . '.png';
            $this->ffmpeg->open($this->getServerLocation())
                ->frame($timecode)
                ->save($filenames[$key]);
            echo "Saved screenshot in " . $filenames[$key] . "\n";
        }
        $this->killPeerflix($pid);
        return $filenames;
    }

    /**
     * @param $time string|null screenshot mark in form "HH:MM:SS"
     * @return array|TimeCode if time is not set the duration will be fetched from the video file
     */
    public function getTimecodes($time)
    {
        if ($time) {
            $timecodes[] = TimeCode::fromSeconds($time);
        } else {
            $timecodes = $this->buildTimecodeArray(Config::get('saeko.frames'), $this->getDuration());
        }
        return $timecodes;
    }

    /**
     * @param $frames int amount of frames to be extracted
     * @param $length int length of the video in seconds
     * @return array Timecode array of timecodes which can be read by FFMpeg
     */
    private function buildTimecodeArray($frames, $length)
    {
        // We don't need the last entry, that's usually a black frame.
        // So that's why we add an extra frame and then pop it off.
        // That way every frame is shifted to the 'left' in time.
        $framesRange = range(1, $frames + 1);
        array_pop($framesRange);

        $spf = $length / max($framesRange);
        $seconds = [];
        foreach ($framesRange as $frame) {
            $seconds[] = TimeCode::fromSeconds($spf * $frame);
        }
        return $seconds;
    }

    /**
     * @return int the duration of the torrent in seconds
     */
    private function getDuration()
    {
        return (int)$this->ffmpeg->getFFProbe()
            ->format($this->getServerLocation())
            ->get('duration');
    }

    /**
     * @param $pid int the pid of a process (hopefully peerflix)
     */
    public function killPeerflix($pid)
    {
        $shell = new Exec();
        $kill = new CommandBuilder('kill');
        $kill->addParam($pid);
        $shell->run($kill);
    }

    /**
     * Save the images to disk
     * @param          $filenames
     * @param \Episode $episode
     */
    public function saveScreenshots($filenames, $episode)
    {
        foreach ($filenames as $filename) {
            $this->screenshotGenerator->make($filename, $episode);
        }
        Log::info('Screenshots processed');;
    }

    private function cleanUp($filenames)
    {
        foreach ($filenames as $filename) {
            if (\File::exists($filename)) {
                \File::delete($filename);
            }
        }
    }


}