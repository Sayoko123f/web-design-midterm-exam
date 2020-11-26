<?php

namespace shinshi\crawler;

class wnacgCrawler extends masterCrawler
{
    protected function set_heading()
    {
        preg_match('/<h2>(.+?)<\/h2>/', $this->html, $matches);
        $this->heading = $matches[1];
    }

    protected function set_sourceTime()
    {
        preg_match('/上傳於(\d{4}-\d\d-\d\d)/', $this->html, $matches);
        $this->sourceTime = $matches[1];
    }

    protected function set_imgUrlList()
    {
        $imgurl = "https://www.wnacg.org/photos-gallery-aid-{$this->id}.html";
        $imghtml = $this->set_html($imgurl);
        preg_match_all('/img2\.wnacg\.download\/data\/\d+?\/\d+?\/.+?\.(jpg|png)/', $imghtml, $matches);
        $this->imgUrlList = $matches;
    }

    public function download_imgs()
    {
        $dirname = $this->createDir();

        $i = 0;
        $imgsFilename = array();

        foreach ($this->imgUrlList[0] as $v) {
            echo 'downloading...' . $i . PHP_EOL;
            $v = 'https://' . $v;
            $file = $this->download_img($v);
            $filename = $dirname . '/' . strval($i) . '.' . $this->imgUrlList[1][$i];
            file_put_contents($filename, $file);
            $imgsFilename[] = strval($i) . '.' . $this->imgUrlList[1][$i];
            $i++;
            sleep($this->sleep);
        }
        $this->export_json($imgsFilename);

        echo 'finished!' . PHP_EOL;
        return true;
    }
}
