<?php

namespace shinshi\crawler;

ini_set('user-agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36');
require_once('master.php');
require_once('wnacg.php');
require_once('nhentai.php');
require_once('ehentai.php');

//$url = 'https://www.wnacg.org/photos-gallery-aid-103122.html';
//$url = 'https://nhentai.net/g/224180/';
//$url = 'https://www.wnacg.org/photos-index-aid-108258.html';
$url='https://www.wnacg.org/photos-index-aid-108202.html';

$t = begin_crawl($url);
if (!$t) {
    die('url parse error:(');
}
$t->getInfo() . PHP_EOL;
$t->download_imgs();


function begin_crawl($url)
{
    // start check url
    $str = parse_url($url);
    if (!isset($str['scheme']) || !isset($str['host']) || !isset($str['path'])) {
        return false;
    }
    if ($str['scheme'] !== 'https') {
        return false;
    }

    switch ($str['host']) {
            //wnacg
        case 'www.wnacg.org':
            if (!preg_match('/photos-(?:gallery|index|slide)-aid-(\d+).html/', $str['path'], $matches)) {
                return false;
            }
            $baseurl = "https://www.wnacg.org/photos-index-aid-{$matches[1]}.html";
            return new wnacgCrawler($baseurl, $matches[1]);

            //nhentai
        case 'nhentai.net':
            if (!preg_match('/nhentai.net\/g\/(\d{1,7})/', $url, $matches)) {
                return false;
            }
            $baseurl = "https://nhentai.net/g/{$matches[1]}/";
            return new nhentaiCrawler($baseurl, $matches[1]);

            //ehentai
        case 'e-hentai.org':
            if (!preg_match('/e-hentai.org\/g\/(\d+?)\/([a-zA-z0-9]+?)\//', $url, $matches)) {
                return false;
            }
            return new ehentaiCrawler($url, array($matches[1], $matches[2]));

        default:
            return false;
    }
}
