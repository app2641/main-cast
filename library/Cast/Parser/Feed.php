<?php


namespace Cast\Parser;

class Feed extends ParserAbstract implements ParserInterface
{

    /**
     * ビデオ新着情報RSS
     *
     * @author app2641
     **/
    protected $url = 'http://www.dmm.co.jp/digital/videoa/-/list/=/rss=create/sort=date/';



    /**
     * RSSを格納するDOMDocumentクラス
     *
     * @author app2641
     **/
    protected $dom;

    
    public function execute ()
    {
        try {
            $xml = file_get_contents($this->url);
            $this->dom = new \DOMDocument();
            $this->dom->loadXML($xml);


            // item要素を解析して新着ビデオを保存する
            $items = $this->dom->getElementsByTagName('item');
            foreach ($items as $item) {
                $this->parseItem($item);
            }
        
        } catch (\Exception $e) {
            throw $e;
        }
    }



    /**
     * RSSのitem要素を解析して主演ビデオをDBに保存する
     *
     * @param DomElement $item  RSSのitem要素
     * @author app2641
     **/
    public function parseItem (\DomElement $item)
    {
        $contents_model = $this->container->get('ContentsModel');
        $cast_model = $this->container->get('CastModel');

        $title = $item->getElementsByTagName('title')->item(0)->nodeValue;
        $contents = $contents_model->query->fetchByTitle($title);

        if ($contents) {
            // 登録済みタイトルは無視
            return false;
        }

        // videoページのurlを取得
        $link = $item->getElementsByTagName('link')->item(0)->nodeValue;

        // videoのタイトルを取得
        $title = $item->getElementsByTagName('title')->item(0)->nodeValue;

        $video = new Video();
        $video->setUrl($link);
        $video->setTitle($title);
        $video->execute();
    }
}

