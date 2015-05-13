<?php

namespace App\Services;

use App\Contracts\FrameCapture;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;


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
     * Set the source location of the video from where the frame should be extracted
     * @param $path
     * @return $this
     */
    public function setSource($path)
    {
        $this->source = $path;
    }

    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param array $timecodes locations in time at which the frames should be extracted
     * @return $this
     */
    public function setTimecodes(array $timecodes)
    {
        $this->timecodes = $timecodes;
    }

    /**
     * @return array|\FFMpeg\Coordinate\TimeCode an array of timecodes which can be read by FFMpeg
     */
    public function getTimecodes()
    {
        return $this->timecodes;
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
        }
        return $filenames;
    }

    /**
     * @param string|array $time the time in HH:MM:SS format
     * @param string $amount the amount of frames to be extracted. This value takes precedence over the time
     *     variable
     * @return mixed
     */
    public function makeTimecodes($time, $amount)
    {
        if ($amount) {
            return $this->timecodesFromAmount($amount);
        }

        return $this->timecodesFromTime($time);
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
     * Generate timecodes based on the amount of frames which have to be extracted
     * @param $amount
     * @return array
     */
    private function timecodesFromAmount($amount)
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
     * Generate timecode from a single time
     * @param $time
     * @return TimeCode
     */
    private function timecodesFromTime($time)
    {
        return TimeCode::fromString($time);

    }
}