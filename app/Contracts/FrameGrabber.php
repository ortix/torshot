<?php

namespace App\Contracts;


interface FrameGrabber
{

    /**
     * Set the source location of the video from where the frame should be extracted
     * @param $path
     * @return $this
     */
    public function setSource($path);

    /**
     *
     * @param $amount
     * @return mixed
     */
    public function timecodesFromAmount($amount);

    /**
     * @param array $timecodes locations in time at which the frames should be extracted
     * @return $this
     */
    public function setTimecodes(array $timecodes);

    /**
     * Capture the frames and save them
     * @param string $location the path to the folder where the frames should be saved
     * @return mixed
     */
    public function extract($location);

}