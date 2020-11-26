<?php

namespace shinshi\crawler;

class nhentaiCrawler extends masterCrawler
{
    private function set_metadata()
    {
        $this->metadata = array();
        $info = $this->html->find('[id=info]', 0);
        $h1 = $info->find('h1', 0);
        $h2 = $info->find('h2', 0);
        if ($h1) {
            $span = $h1->find('span');
            foreach ($span as $v) {
                $this->metadata[] = $v->plaintext;
            }
        }
        if ($h2) {
            $span = $h2->find('span');
            foreach ($span as $v) {
                $this->metadata[] = $v->plaintext;
            }
        }
        $this->heading = $h2 ? $h2->plaintext : $h1->plaintext;
    }

    protected function set_heading()
    {
        $this->set_metadata();
    }

    protected function set_sourceTime()
    {
        preg_match('/datetime="(\d{4}-\d\d-\d\d)/', $this->html, $matches);
        $this->sourceTime = $matches[1];
    }

    protected function set_imgUrlList()
    {
        $find_thumbs = $this->html->find('[class=thumbs]', 0)->find('img');
        foreach ($find_thumbs as $v) {
            if (preg_match('/<img src="(.+?)"/', $v, $matches)) {
                $this->imgUrlList[] = preg_replace(array('/t.nhentai.net/', '/t\.jpg/'), array('i.nhentai.net', '.jpg'), $matches[1]);
            }
        }
    }

    public function download_imgs()
    {
        $dirname = $this->createDir();
        $i = 0;
        $imgsFilename = array();

        foreach ($this->imgUrlList as $v) {
            echo 'downloading...' . $i . PHP_EOL;
            $file = $this->download_img($v);
            $filename = $dirname . '/' . strval($i) . '.jpg';
            file_put_contents($filename, $file);
            $imgsFilename[] = strval($i) . '.jpg';
            $i++;
            sleep($this->sleep);
        }
        $this->export_json($imgsFilename);
        echo 'finished!' . PHP_EOL;
        return true;
    }
}
