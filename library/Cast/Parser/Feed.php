<?php


namespace Cast\Parser;

use Cast\Container,
    Cast\Factory\ModelFactory;

class Feed extends ParserAbstract implements ParserInterface
{

    /**
     * @String

     * ビデオ新着情報RSS
     **/
    protected $url = 'http://www.dmm.co.jp/digital/videoa/-/list/=/rss=create/sort=date/';



    /**
     * @DOMDocument

     * RSSを格納するDOMDocumentクラス
     **/
    protected $dom;



    /**
     * 解析の実行
     *
     * @return void
     **/
    public function execute ()
    {
        try {
            // RSSをDOMに格納する
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
     * @return void
     **/
    public function parseItem (\DomElement $item)
    {
        $contents_model = $this->container->get('ContentsModel');
        $cast_model = $this->container->get('CastModel');

        $title = trim($item->getElementsByTagName('title')->item(0)->nodeValue);
        $contents = $contents_model->query->fetchByTitle($title);

        if ($contents) {
            // 登録済みタイトルは無視
            return false;
        }

        // videoページのurlを取得
        $url = $item->getElementsByTagName('link')->item(0)->nodeValue;

        // 動画情報を取得する
        try {
            $video = new Video();
            $video->setUrl($url);
            $video->setTitle($title);
            $video->execute();
        
        } catch (\Exception $e) {
            echo $title.' ページの取得に失敗しました！'.PHP_EOL.
                $e->getMessage().PHP_EOL;
        }
    }
}

