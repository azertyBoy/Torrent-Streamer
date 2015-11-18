<?php

namespace Homesoft\Bundle\TorrentStreamerBundle\Utils;

use \Guzzle\Service\Client as RestClient;

class T411RestClient {

    const T411_API_BASE_URL = 'https://api.t411.in';

    /**
     * @var RestClient $restClient
     */
    private $restClient;

    /**
     * @var string $token
     */
    private $token;

    /**
     * @var string $tmpFolder
     */
    private $tmpFolder;

    /**
     * @var string username
     */
    private $username;

    /**
     * @var string password
     */
    private $password;

    public function __construct(RestClient $restClient, $username, $password, $tmpFolder)
    {
        $this->restClient = $restClient;
        $this->tmpFolder = $tmpFolder;
        $this->username = $username;
        $this->password = $password;
        $this->authentification();
    }

    private function handleResponse($result)
    {
        if(isset($result->code)){
            $details = '(code = ' . $result->code .' ; message = '. $result->code .')';
            throw new \Exception('Impossible de se connecter à l\'API T411 '. $details .'.');
        }
    }

    public function authentification($force = false)
    {
        if(!empty($this->token) && $force == false)
            return true;
        $request = $this->restClient ->post(self::T411_API_BASE_URL.'/auth');
        $request->addPostFields(array('username' => $this->username, 'password' => $this->password));
        $response = $request->send();
        $result = json_decode($response->getBody(true));
        $this->handleResponse($result);
        $this->token = $result->token;
        return $result;
    }

    public function search($tags)
    {
        $request = $this->restClient->get(self::T411_API_BASE_URL.'/torrents/search/' . $tags);
        $request->addHeader('Authorization', $this->token);
        $response = $request->send();
        $result = json_decode($response->getBody(true));
        $this->handleResponse($result);
        return $result;
    }

    public function torrentDetails($torrentId)
    {
        $request = $this->restClient->get(self::T411_API_BASE_URL.'/torrents/details/'.$torrentId);
        $request->addHeader('Authorization', $this->token);
        $response = $request->send();
        $result = json_decode($response->getBody(true));
        $this->handleResponse($result);
        return $result;
    }

    public function downloadTorrent($torrentId)
    {
        $request = $this->restClient->get(self::T411_API_BASE_URL.'/torrents/download/'.$torrentId);
        $request->addHeader('Authorization', $this->token);
        $response = $request->send();
        $result = $response->getBody(true);
        $this->handleResponse($result);
        //Enregistre le torrent quelque part
        $detailsTorrent = $this->torrentDetails($torrentId);
        $torrentPath = $this->tmpFolder.'/'.$detailsTorrent->rewritename.'.torrent';
        file_put_contents($torrentPath, $result);
        return $torrentPath;
    }
}