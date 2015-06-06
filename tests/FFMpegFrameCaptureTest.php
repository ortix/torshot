<?php

class FFMpegFrameCaptureTest extends TestCase
{

    private $location;

    public function setUp()
    {
        $this->location =   '/var/www/torshot/tmp';
        parent::setUp();
    }

    public function tearDown()
    {
        $files = File::allFiles($this->location);
        foreach ($files as $file) {
            File::delete($file);
        }
        parent::tearDown();
    }

    public function testExtract()
    {
        $capture = new \App\Services\FFMpegFrameCapture();
        $source = base_path() . '/ffmpeg/test_vid.mp4';
        $location = base_path() . '/tmp';
        $amount = 3;

        $capture->setSource($source);
        $timecodes = $capture->timecodesFromAmount($amount);
        $capture->setTimecodes($timecodes);
        $files = $capture->extract($location);

        $this->assertEquals($amount, count($files));
        $this->assertEquals($amount, count(File::allFiles($location)));
    }

    public function testMakeTimecodesWithAmount()
    {
        // set source
        // pass time and amount
        $capture = new \App\Services\FFMpegFrameCapture();
        $source = base_path() . '/ffmpeg/test_vid.mp4';

        $capture->setSource($source);
        $this->assertEquals($capture->getSource(), $source);

        $amount = 4;
        $timecodes = $capture->timecodesFromAmount($amount);
        $this->assertContainsOnlyInstancesOf('FFMpeg\Coordinate\TimeCode', $timecodes);
    }
}
