<?php

namespace App\Services;

use App\Console\Commands\CommandBuilder;
use AdamBrett\ShellWrapper\Runners\Exec;
use App\Contracts\TorrentStreamer;
use GuzzleHttp\Client;

class PeerflixStreamer implements TorrentStreamer
{

    protected $port;
    protected $torrent;
    protected $hostname;
    protected $pid;
    private $guzzle;

    function __construct(Client $guzzle)
    {

        $this->guzzle = $guzzle;
    }

    /**
     * @param string $torrent the full path to the torrent or magnet URL
     * @return $this
     */
    public function torrent($torrent)
    {
        $this->torrent = $torrent;
        return $this;
    }

    /**
     * Set the port number on which the torrent will be streamed to. This is NOT the port number for TCP/UDP
     * connections but the port from which the torrent stream can be read.
     * @param int $port
     * @return $this
     */
    public function port($port)
    {

        $this->port = $port;
        return $this;

    }

    /**
     * Set the host (IP address) on which the torrent will be streamed to.
     * @param string $hostname
     * @return $this
     */
    public function hostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * Run the streaming service
     * @return void
     */
    public function run()
    {
        $this->pid = $this->startPeerflix();

        // wait for peerflix to spin up
        $status = false;
        while ($status != 200) {
            try {
                $status = $this->guzzle->head($this->getServerLocation())->getStatusCode();
            } catch (\Exception $e) {
                // echo what's wrong and sleep
                echo $e->getMessage() . "\n";
                sleep(1);
            }
        }
    }

    /**
     * Build the command and start the peerflix server
     * @return int $pid the process ID of peerflix
     */
    public function startPeerflix()
    {
        $shell = new Exec();
        $command = new CommandBuilder('node');
        $command->addSubCommand(base_path() . '/node_modules/peerflix/app.js')
            ->addParam($this->torrent)
            ->addArgument('hostname', $this->hostname)
            ->addArgument('port', $this->port)
            ->addFlag('q')
            ->addFlag('r')
            ->toBackground();
        $shell->run($command);
        $pid = $shell->getOutput()[0]; // get pid of peerflix so we can kill later
        return (int) $pid;
    }

    /**
     * Kill the peerflix process
     */
    public function kill() {
        $shell = new Exec();
        $kill = new CommandBuilder('kill');
        $kill->addParam($this->pid);
        $shell->run($kill);
    }

    /**
     * @return string the http location of the server
     */
    public function getServerLocation()
    {
        return 'http://' . $this->hostname . ':' . $this->port;
    }
}