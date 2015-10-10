<?php

namespace App\Services;

use App\Contracts\FrameCapture;
use App\Events\SingleFrameCaptured;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;


class FFMpegFrameCapture implements FrameCapture
{
    private $source;
    private $timecodes;
    private $ffmpeg;

    /**
     * FFMpegFrameCapture constructor.
     */
    public function __construct()
    {
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => \Config::get('torshot.ffmpeg_binaries'),
            'ffprobe.binaries' => \Config::get('torshot.ffprobe_binaries'),
        ]);
    }

    /**
     * @return array|\FFMpeg\Coordinate\TimeCode an array of timecodes which can be read by FFMpeg
     */
    public function getTimecodes()
    {
        return $this->timecodes;
    }

    /**
     * @param array $timecodes locations in time at which the frames should be extracted
     * @return $this
     */
    public function setTimecodes(array $timecodes)
    {
        $this->timecodes = $timecodes;
        return $this;
    }

    /**
     * Capture the frames and save them
     * @param string $location the path to the folder where the frames should be saved
     * @param array $params Not used yet. For future filename prefixes etc.
     * @return array the paths to the location of the screenshots
     */
    public function extract($location, $params = array())
    {
        $filenames = []; // create empty array
        foreach ($this->timecodes as $key => $timecode) {
            $filenames[$key] = $location . '/' . md5(time()) . $key . '.png';
            $this->ffmpeg->open($this->getSource())
                ->frame($timecode)
                ->save($filenames[$key]);

            // Immediately notify the user that a frame has been captured
            event(new SingleFrameCaptured($filenames[$key]));
        }
        return $filenames;
    }

    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the source location of the video from where the frame should be extracted
     * @param $path
     * @return $this
     */
    public function setSource($path)
    {
        $this->source = $path;
    }

    /**
     * Generate timecodes based on the amount of frames which have to be extracted
     * @param $amount
     * @return array
     */
    public function timecodesFromAmount($amount)
    {
        $duration = $this->getDuration();
        $frames = range(1, $amount);
        $seconds = [];
        foreach ($frames as $frame) {
            $seconds[] = TimeCode::fromSeconds($duration * ($frame) / ($amount + 1));
        }
        return $seconds;
    }

    /**
     * @return int the duration of the video in seconds
     */
    private function getDuration()
    {
        return (int)$this->ffmpeg->getFFProbe()
            ->format($this->getSource())
            ->get('duration');
    }

    /**
     * Generate timecode from a single time
     * @param $time
     * @return TimeCode
     */
    public function timecodesFromTime($time)
    {
        return TimeCode::fromString($time);

    }
}