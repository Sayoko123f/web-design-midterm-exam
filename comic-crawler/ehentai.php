<?php

namespace shinshi\crawler;

class ehentaiCrawler extends masterCrawler
{
    private $info;

    private function set_metadata()
    {
        $this->metadata = array();
        $this->info = $this->html->find('[class=gm]', 0);
        $h1 = $this->info->find('[id=gn]', 0)->plaintext;
        $h2 = $this->info->find('[id=gj]', 0)->plaintext;
        if ($h1) {
            $this->metadata[] = $h1;
        }
        if ($h2) {
            $this->metadata[] = $h2;
        }

        $this->heading = $h2 ? $h2 : $h1;
    }

    protected function set_heading()
    {
        $this->set_metadata();
    }
    protected function set_sourceTime()
    {
        preg_match('/<td class="gdt2">(\d{4}-\d\d-\d\d)/', $this->info, $matches);
        $this->sourceTime = $matches[1];
    }

    protected function set_imgUrlList()
    {
        $this->imgUrlList = array();
        preg_match('/<td class="gdt2">(\d+?) pages/', $this->info, $matches);
        $length = $matches[1];
        $p = $length % 40 ? $length / 40 + 1 : $length / 40;
        for ($i = 0; $i < $p; $i++) {
            if ($i) {
                $url = $this->url . "?p={$i}";
                $this->html = $this->set_html($url);
            }
            $a = $this->html->find('[id=gdt]', 0)->innertext;
            preg_match_all('/href="(https:\/\/e-hentai.org\/s.+?)">/', $a, $matches);
            $this->imgUrlList[] = $matches[1];
            sleep($this->sleep);
        }
        $this->imgUrlList = array_unique(array_reduce($this->imgUrlList, 'array_merge', array()));
    }


    public function download_imgs()
    {
        $dirname = $this->createDir();
        $i = 0;
        $imgsFilename = array();
        foreach ($this->imgUrlList as $v) {
            echo 'downloading...' . $i . PHP_EOL;
            $img = $this->set_html($v)->find('[id=img]', 0);
            preg_match('/src="(.+?)"/', $img, $matches);
            $file = $this->download_img($matches[1]);
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
