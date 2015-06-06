<?php

namespace App\Contracts;


interface TorrentStreamer
{

    /**
     * @param string $torrent the full path to the torrent or magnet URL
     * @return $this
     */
    public function torrent($torrent);

    /**
     * Set the port number on which the torrent will be streamed to. This is NOT the port number for TCP/UDP
     * connections but the port from which the torrent stream can be read.
     * @param int $port
     * @return $this
     */
    public function port($port);

    /**
     * Set the host (IP address) on which the torrent will be streamed to.
     * @param string $hostname
     * @return $this
     */
    public function hostname($hostname);

    /**
     * Run the streaming service
     * @return void
     */
    public function run();

    /**
     *
     * @return void
     */
    public function kill();

}