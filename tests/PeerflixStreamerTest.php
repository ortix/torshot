<?php

class PeerflixStreamerTest extends TestCase
{
    public function testGetServerLocation()
    {
        $streamer = App::make('App\Services\PeerflixStreamer');

        $streamer->hostname('127.0.0.1')->port(8888);
        $location = $streamer->getServerLocation();

        $this->assertEquals('http://127.0.0.1:8888', $location);

    }

    public function testStartPeerflix()
    {
        /** @var App\Services\PeerflixStreamer $streamer */
        $streamer = App::make('App\Services\PeerflixStreamer');
        $torrent = "magnet:?xt=urn:btih:1A16AFA4F2BCEB2D289F1E075E2BB08980C5D954&dn=the+big+bang+theory+s08e24+hdtv+x264+lol+ettv&tr=udp%3A%2F%2Fopen.demonii.com%3A1337%2Fannounce";
        $streamer->hostname('127.0.0.1')->port(8888)->torrent($torrent);
        $pid = $streamer->startPeerflix();
        dd($pid);
    }
}
