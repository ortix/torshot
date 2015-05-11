<?php

class FFMpegFrameCaptureTest extends TestCase
{

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $response = $this->call('GET', '/');
        $this->assertEquals(200, $response->getStatusCode());

    }

    public function testMakeTimecodes()
    {
        // set source
        // pass time and amount

        $capture = new \App\Services\FFMpegFrameCapture();
        $source = base_path() . '/ffmpeg/test_vid.mp4';

        $capture->setSource($source);
        $this->assertEquals($capture->getSource(), $source);

        $amount = 4;
        $timecodes = $capture->makeTimecodes(null, $amount);
        $this->assertContainsOnlyInstancesOf('FFMpeg\Coordinate\TimeCode', $timecodes);
    }

}
