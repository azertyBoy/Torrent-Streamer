<?php

namespace Homesoft\Bundle\TorrentStreamerBundle\Utils;

class TorrentStreamer {

    /**
     * @var string $rootDir
     */
    private $rootDir;

    private function stopPeerflix()
    {
        shell_exec('killall -9 peerflix');
        sleep(1);
    }

    private function buildStreamUrl()
    {
        $output = shell_exec('ifconfig eth0 | grep "inet adr:"');
        $output = substr($output, strpos($output, 'inet adr:') + strlen('inet adr:'));
        $ipAdress = substr($output, 0, strpos($output, ' '));
        $url = 'http://'.$ipAdress.':8888/';
        return $url;
    }

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function startStreamer($torrent)
    {
        $this->stopPeerflix();
//        die('screen -dmS peerflix bash -c \''.$this->rootDir.'/../bin/playTorrent.sh "' . $torrent . '"\'');
//        shell_exec('screen -dmS peerflix -c \'bash '.$this->rootDir.'/../bin/playTorrent.sh "' . $torrent . '"\'');
        shell_exec('screen -dmS peerflix bash -c \'peerflix "' . $torrent . '"\'');
        sleep(2);
        return $this->buildStreamUrl();
    }

    public function startPlayer($torrentFilePath)
    {
        $this->stopPeerflix();
        $command = 'screen -dmS peerflix bash -c \'peerflix "' . $torrentFilePath . '" --mplayer\'';
        shell_exec($command);
    }
}