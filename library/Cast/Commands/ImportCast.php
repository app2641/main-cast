<?php


namespace Cast\Commands;

use Cast\Container,
    Cast\Factory\ModelFactory;

require_once ROOT_PATH.'/library/SimpleHtmlDomParser/simple_html_dom.php';

class ImportCast extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            echo '廉価版'.PHP_EOL;
            exit();
            set_time_limit(0);

            $this->initDatabaseConnection();
            $conn = \Zend_Registry::get('db');
            $conn->beginTransaction();

            $container  = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            // 別名女優のデータ保管用配列
            $other_casts = array();

            if (! isset($params[0])) {
                throw new \Exception('かな指定必須');
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
                $other_data = array();

                $name = trim($this->_encode($cast->plaintext));
                preg_match('/（.*）/', $name, $matches);

                // 別名を持つ女優かを確認
                if (count($matches) != 0) {
                    // 別名を保管
                    $other_name = preg_replace('/(（|）)/', '', $matches[0]);
                    $other_data['name'] = $other_name;

                    $name = str_replace('（'.$other_name.'）', '', $name);
                }


                $profile_link = $cast->getAttribute('href');
                preg_match('/id=[0-9]*/', $profile_link, $matches);
                $cast_id = str_replace('id=', '', $matches[0]);


                // AV女優画像へのリンクを取得
                $img = $cast->first_child();
                $img_src = preg_replace('/\/(thumbnail|medium)/', '', $img->getAttribute('src'));

                // AV女優画像のダウンロード
                $download_path = ROOT_PATH.'/public_html/resources/image/cast/'.md5($name).'.jpg';
                if (! file_exists($download_path)) {
                    $command = sprintf('curl %s -o %s', $img_src, $download_path);
                    exec($command);

                    if (isset($other_data['name'])) {
                        $other_data['img'] = $download_path;
                    }
                }


                // キャスト情報が既にあるかを確認
                $data = $cast_model->query->fetchByName($name);
                if (! $data) {
                    // プロフィールページの取得
                    $profile_url = sprintf('http://actress.dmm.co.jp/-/detail/=/actress_id=%s', $cast_id);
                    $profile = file_get_html($profile_url);
                    if ($profile == false) {
                        echo $profile_url.PHP_EOL;
                        echo 'プロフィールページが取得出来ませんでした'.PHP_EOL;
                        continue;
                    }


                    $furigana = $this->_encode($profile->find('table tbody tr td h1', 0)->plaintext);

                    // 別名を持つか持たないかで正規表現を切り替える
                    if (isset($other_data['name'])) {
                        $other_furigana = str_replace($name.'（'.$other_data['name'].'）', '', $furigana);
                        $other_furigana = preg_replace('/^（[^（]*（/', '', $other_furigana);
                        $other_furigana = str_replace('））', '', $other_furigana);
                    
                        $other_data['furigana'] = $other_furigana;
                        $other_data['url'] = $profile_url;

                        $furigana = str_replace($name.'（'.$other_data['name'].'）（', '', $furigana);
                        $furigana = preg_replace('/（.*/', '', $furigana);

                    } else {
                        preg_match('/(（|\().*(）|\))/', $furigana, $matches);
                        $furigana = preg_replace('/(（|）)/', '', $matches[0]);
                    }



                    $params = new \stdClass;
                    $params->name = $name;
                    $params->furigana = $furigana;
                    $params->url = $profile_url;
                    $cast_model->insert($params);

                    $profile->clear();
                }

                unset($img);


                if (isset($other_data['name'])) {
                    $other_casts[] = $other_data;
                }
            }

            unset($casts, $cast);
            $list->clear();



            // 別名女優の処理
            foreach ($other_casts as $cast) {
                $data = $cast_model->query->fetchByName($cast['name']);

                if (! $data) {
                    $params = new \stdClass;
                    $params->name = $cast['name'];
                    $params->furigana = $cast['furigana'];
                    $params->url = $cast['url'];
                    $cast_model->insert($params);
                }
            }


            $conn->commit();
        
        } catch (\Exception $e) {
            $conn->rollBack();
            $this->errorLog($e->getMessage());
        }
    }



    /**
     * エンコード
     *
     * @author app2641
     **/
    private function _encode ($string)
    {
        return mb_convert_encoding($string, 'UTF-8', 'EUC-JP');
    }



    /**
     * コマンドリストに表示するヘルプメッセージを表示する
     *
     **/
    public static function help ()
    {
        /* write help message */
        $msg = '引数に指定した頭文字の女優をインポートする';

        return $msg;
    }
}
