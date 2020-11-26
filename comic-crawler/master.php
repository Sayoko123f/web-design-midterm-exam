<?php

namespace shinshi\crawler;

use stdClass;

require_once('simple_html_dom.php');

abstract class masterCrawler
{
    protected $sleep = 0.3; //for downloadimg sleep

    protected $url;
    protected $heading;
    protected $sourceTime;
    protected $hash;
    protected $metadata;

    // result not need
    protected $html;
    protected $id;
    protected $imgUrlList; //for set_imgUrlList()
    public function __construct($url, $id)
    {
        $this->url = $url;
        $this->id = $id;
        $this->hash = hash('ripemd160', $this->url);
        $this->html = $this->set_html($this->url);
        //echo $this->html;
        $this->set_heading();
        echo $this->heading . PHP_EOL;
        $this->set_sourceTime();
        //echo $this->sourceTime;

        $this->set_imgUrlList();
        if (!$this->check_info()) {
            return false;
        }
    }
    abstract protected function set_heading();
    abstract protected function set_sourceTime();
    abstract protected function set_imgUrlList();
    abstract public function download_imgs();
    protected function set_html($url)
    {
        try {
            $html = file_get_html($url);
            return $html;
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            return false;
        }
    }
    protected function createDir()
    {
        $dirname = 'ressha/public/img/' . $this->hash;
        if (!is_dir('ressha')) {
            mkdir('ressha');
        }
        if (!is_dir('ressha/public')) {
            mkdir('ressha/public');
        }
        if (!is_dir('ressha/public/img')) {
            mkdir('ressha/public/img');
        }
        if (!is_dir($dirname)) {
            echo 'mkdir' . PHP_EOL;
            mkdir($dirname);
        }
        return $dirname;
    }

    public function getInfo()
    {
        $info = new stdClass();
        $info->url = $this->url;
        $info->heading = $this->heading;
        $info->sourceTime = $this->sourceTime;
        $info->hash = $this->hash;
        if ($this->metadata) {
            $info->metadata = $this->metadata;
        }
        var_dump($info);
        return json_encode($info);
    }

    protected function check_info()
    {
        /*
        echo 'url:' . $this->url . PHP_EOL;
        echo 'id:' . $this->id . PHP_EOL;
        echo 'heading:' . $this->heading . PHP_EOL;
        echo 'sourceTime:' . $this->sourceTime . PHP_EOL;
        */
        if (!$this->url && !$this->heading && !$this->sourceTime) {
            echo 'Error: check_info() is failed in 1, stop download:(' . PHP_EOL;
            return false;
        }
        if (!$this->imgUrlList) {
            /*
            echo 'Error: check_info() is failed in 2, stop download:(' . PHP_EOL;
            echo 'imgUrlList:' . $this->imgUrlList PHP_EOL;
*/
            return false;
        }
        echo 'check_info() ok.' . PHP_EOL;
        return true;
    }

    protected function download_img($imgurl)
    {
        try {
            $file = file_get_contents($imgurl);
        } catch (\Exception $e) {
            echo 'download failed: ' . $imgurl . PHP_EOL;
            echo 'ErrorMessage: ' . $e->getMessage() . PHP_EOL;
            echo 'ErrorCode: ' . $e->getCode() . PHP_EOL;
        }
        return $file;
    }

    protected function sendHttpPostJsonRequest($url, $json)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json)
            )
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    protected function jsonSample()
    {
        $obj = new stdClass();
        $metaObj = new stdClass();
        $metaObj->authors = array();
        $metaObj->lang = 0;
        $metaObj->introduction = "";
        $metaObj->tags = array();
        $metaObj->categories = "";
        $metaObj->sourceTime = "";
        $obj->path = $this->hash;
        $obj->title = $this->heading;
        $obj->url = $this->url;
        $obj->imgsLen = 0;
        $obj->imgs = array();
        $obj->meta = $metaObj;
        return $obj;
    }

    protected function export_json($imgsFilename)
    {
        if (!is_array($imgsFilename)) {
            return false;
        }
        $obj = $this->jsonSample();
        $obj->meta->sourceTime = $this->sourceTime;
        $obj->imgsLen = count($imgsFilename);
        $obj->imgs = $imgsFilename;
        $json = json_encode($obj);
        $dirname = $this->createDir() . '/meta.json';
        $handle = fopen($dirname, 'w');
        fwrite($handle, $json);
        fclose($handle);

        $this->append_albumList(array($this->hash,0));
    }

    protected function append_albumList($field)
    {
        if (!is_array($field)) {
            $tmp = strval($field);
            $field = array();
            $field[] = $tmp;
        }
        $csv = 0;
        if (!file_exists('ressha/public/img/list.csv')) {
            $csv = fopen('ressha/public/img/list.csv', 'x');
            fclose($csv);
        }
        $csv = fopen('ressha/public/img/list.csv', 'a');
        fputcsv($csv, $field);
        fclose($csv);
    }
}
