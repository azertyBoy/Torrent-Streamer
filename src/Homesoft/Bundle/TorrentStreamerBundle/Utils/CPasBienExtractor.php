<?php

namespace Homesoft\Bundle\TorrentStreamerBundle\Utils;
use Cocur\Slugify\Slugify;
use Sunra\PhpSimple\HtmlDomParser;

class CPasBienExtractor {

    const CPASBIEN_BASE_URL = 'http://www.cpasbien.io';

    /**
     * @var Slugify $slugifier
     */
    private $slugifier;

    /**
     * @var string $tmpFolder
     */
    private $tmpFolder;


    public function __construct(Slugify $slugifier, $tmpFolder)
    {
        $this->slugifier = $slugifier;
        $this->tmpFolder = $tmpFolder;
    }

    public function search($tags)
    {
        $searchUrl = self::CPASBIEN_BASE_URL . '/recherche/' . $this->slugifier->slugify($tags) . '.html';
        $dom = HtmlDomParser::file_get_html($searchUrl);
        $lignes0 = $dom->find('div[class=ligne0]');
        $lignes1 = $dom->find('div[class=ligne1]');

        $torrentsLigne0 = array();
        foreach($lignes0 as $torrentLigne0)
            array_push($torrentsLigne0, $this->parseHtmlToTorrent($torrentLigne0));
        $torrentsLigne1 = array();
        foreach($lignes1 as $torrentLigne1)
            array_push($torrentsLigne1, $this->parseHtmlToTorrent($torrentLigne1));

        //merge array to respect website order result
        $result = array();
        $index0 = $index1 = 0;
        for($i = 0; $i < (count($torrentsLigne0) + count($torrentsLigne1)); $i++) {
            if($i % 2 == 0){
                array_push($result, $torrentsLigne0[$index0]);
                $index0++;
            }
            else{
                array_push($result, $torrentsLigne1[$index1]);
                $index1++;
            }
        }

        return $result;
    }

    public function parseHtmlToTorrent($torrentDom)
    {
        $result = new \stdClass();
        $result->title = $torrentDom->find('a', 0)->innertext;
        $result->urlCard = $torrentDom->find('a', 0)->href;
        $size = $torrentDom->find('div[class=poid]', 0)->plaintext;
        $result->size = substr($size, 0, strlen($size)-6);
        $result->seeders = $torrentDom->find('span[class=seed_ok]', 0)->plaintext;
        $result->leechers = $torrentDom->find('div[class=down]', 0)->plaintext;
        return $result;
    }

    public function downloadTorrentFile($torrentUrlCard)
    {
        $dom = HtmlDomParser::file_get_html($torrentUrlCard);
        $urlTorrentFile = $dom->find('a[id=telecharger]', 0)->href;
        $urlTorrentFile = self::CPASBIEN_BASE_URL . $urlTorrentFile;

        $curl = curl_init($urlTorrentFile);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        $fileContent = curl_exec($curl);
        curl_close($curl);

        $filename = $torrentUrlCard;
        while(strpos($filename, '/') !== false) {
            $test = strpos($filename, '/');
            $filename = substr($filename, strpos($filename, '/')+1);
        }
        $test = strpos($filename, '/');
        $filename = substr($filename, 0, strlen($filename) - 5) . '.torrent';
        $filePath = $this->tmpFolder . '/' . $filename;

        file_put_contents($filePath, $fileContent);
        return $filePath;
    }
}