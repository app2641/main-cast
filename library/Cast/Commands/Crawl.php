<?php


namespace Cast\Commands;

use Cast\Parser\Feed;
use Cast\Aws\SES;

use Cast\Utility\SuperException;

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
                        'url: '.$val['url'].PHP_EOL.PHP_EOL;
                }
            }


            // キャスト情報
            if (\Zend_Registry::isRegistered('cast')) {
                $data = \Zend_Registry::get('cast');
                $info .= PHP_EOL.'Casts'.PHP_EOL;

                foreach ($data as $val) {
                    $info .= 'name: '.$val['name'].PHP_EOL.
                        'dmm_name: '.$val['dmm_name'].PHP_EOL;
                }
            }


            // 更新情報をメールで送信する
            if ($info != '') {
                $this->sendInformationMail($info);
            }

            $this->log('finished parse!', 'message');
        
        } catch (\Exception $e) {
            SuperException::mail($e);
            $this->errorLog($e->getMessage());
        }
    }



    /**
     * 解析した情報をメールで送信する
     *
     * @param String $info
     * @return void
     **/
    public function sendInformationMail ($info)
    {
        $body = date('Y-m-d H:i:s').' 新着情報'.PHP_EOL.PHP_EOL.$info;

        $SES  = new SES();
        $SES->mail($body);
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
