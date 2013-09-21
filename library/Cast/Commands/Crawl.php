<?php


namespace Cast\Commands;

use Cast\Parser\Feed;

class Crawl extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            set_time_limit(0);

            $this->initDatabaseConnection();

            $feed = new Feed();
            $feed->execute();


            // 更新した情報をまとめる
            $info = '';

            // ビデオ情報
            if (\Zend_Registry::isRegistered('video')) {
                $data = \Zend_Registry::get('video');
                $info .= 'Videos'.PHP_EOL;

                foreach ($data as $val) {
                    $info .= 'title: '.$val['title'].PHP_EOL.
                        'cast: '.$val['cast'].PHP_EOL.
                        'url: '.$val['url'].PHP_EOL.PHP_EOL;
                }
            }

            // キャスト情報
            if (\Zend_Registry::isRegistered('cast')) {
                $data = \Zend_Registry::get('cast');
                $info .= 'Casts'.PHP_EOL;

                foreach ($data as $val) {
                    $info .= 'name: '.$val['name'].PHP_EOL.
                        'furigana: '.$val['furigana'].PHP_EOL.
                        'url: '.$val['url'].PHP_EOL.PHP_EOL;
                }
            }

            echo $info.PHP_EOL;


            $this->log('finished parse!', 'message');
        
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }



    /**
     * コマンドリストに表示するヘルプメッセージを表示する
     *
     **/
    public static function help ()
    {
        /* write help message */
        $msg = 'DMMの新着情報RSSを解析してDBへ保存する';

        return $msg;
    }
}
