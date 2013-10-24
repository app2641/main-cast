<?php


namespace Cast\Commands;

use Cast\Container,
    Cast\Factory\ModelFactory;

use Cast\Parser\Profile;

require_once ROOT_PATH.'/library/SimpleHtmlDomParser/simple_html_dom.php';

class ImportCasts extends Base\AbstractCommand
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
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();

            $container  = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            if (! isset($params[0])) {
                throw new \Exception('仮名文字を指定してください');
            }


            $kana = $params[0];
            $url  = sprintf('http://www.dmm.co.jp/mono/dvd/-/actress/=/keyword=%s', $kana);

            $list = file_get_html($url);
            if ($list === false) {
                echo $url.PHP_EOL;
                throw new \Exception('リストページを取得出来ませんでした');
            }

            // 女優を取得する
            $casts = $list->find('div.act-box li a');
            foreach ($casts as $cast) {
                $profile_link = $cast->getAttribute('href');
                preg_match('/id=[0-9]*/', $profile_link, $matches);
                $cast_id = str_replace('id=', '', $matches[0]);

                $profile = new Profile;
                $profile->setCastId($cast_id);
                $cast = $profile->execute();
                unset($profile);

                if (! $cast) {
                    echo 'キャスト情報取得に失敗しました'.PHP_EOL;
                    echo $profile_link.PHP_EOL.PHP_EOL;
                    continue;
                }
            }

            $this->log('imported!', 'success');
            $db->commit();
        
        } catch (\Exception $e) {
            $db->rollBack();
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
        $msg = '引数に指定したかな文字名のキャストをDMMから取得する';

        return $msg;
    }
}
