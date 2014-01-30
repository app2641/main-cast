<?php


namespace Cast\Parser;

use Cast\Aws\S3;

class Profile extends ParserAbstract implements ParserInterface
{

    /**
     * プロフィールページのURL
     *
     * @author app2641
     **/
    protected $url = 'http://actress.dmm.co.jp/-/detail/=/actress_id=%s';



    /**
     * キャストレコード
     *
     * @author app2641
     **/
    protected $cast = false;



    /**
     * キャストID
     *
     * @author app2641
     **/
    protected $cast_id;



    /**
     * 加工前のキャスト名
     *
     * @author app2641
     **/
    protected $raw_name;



    /**
     * キャストの名前
     *
     * @author app2641
     **/
    protected $name;



    /**
     * キャスト名のふりがな
     *
     * @author app2641
     **/
    protected $furigana;



    /**
     * キャストIDをセットする
     *
     * @param int $cast_id  キャストID
     * @author app2641
     **/
    public function setCastId ($cast_id)
    {
        $this->cast_id = $cast_id;
        $this->url = sprintf($this->url, $cast_id);
    }



    /**
     * 解析処理
     *
     * @return void
     * @return CastModel
     **/
    public function execute ()
    {
        try {
            set_time_limit(0);
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();
        
            $this->parsePage();
            $this->parseName();
            $this->parseFurigana();
            $this->parseCastImage();
            $this->InsertCast();
            
            $db->commit();
        
        } catch (\Exception $e) {
            $db->rollBack();
            echo $e->getMessage().PHP_EOL;
        }

        return $this->cast;
    }



    /**
     * ページの取得
     *
     * @return boolean
     **/
    public function parsePage ()
    {
        try {
            if (is_null($this->cast_id)) {
                throw new \Exception('DMM用のキャストIDが指定されていません！');
            }

            $this->html = file_get_html($this->url);

            if ($this->html == false) {
                throw new \Exception('プロフィールページの取得に失敗しました！');
            }    
        
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }




    /**
     * キャスト名を解析して取得する
     *
     * @return String
     **/
    public function parseName ()
    {
        try {
            $this->raw_name = trim($this->_encode($this->html->find('table tbody tr td h1', 0)->plaintext));
            $this->name = preg_replace('/（.*$/', '', $this->raw_name);
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $this->name;
    }



    /**
     * キャスト名のふりがなを解析して取得する
     *
     * @return String
     **/
    public function parseFurigana ()
    {
        preg_match('/（(.*)）/', $this->raw_name, $matches);
        $this->furigana = $matches[1];

        return $this->furigana;
    }



    /**
     * プロフィール画像を取得して保存する
     *
     * @return boolean
     **/
    public function parseCastImage ()
    {
        try {
            // 画像パスを取得
            $img = $this->html->find('table tbody tr td img[width="125"]', 0);
            if ($img == false) {
                throw new \Exception('プロフィール画像の取得に失敗しました '.$this->cast_id);
            }

            $img_src = $img->getAttribute('src');
            $img_name = md5($this->name);
            $parent_dir = substr($img_name, 0, 1);
            $download_path = '/tmp/cast/'.$img_name.'.jpg';

            if (! is_dir('/tmp/cast')) {
                mkdir('/tmp/cast');
                chmod('/tmp/cast', 0777);
            }


            // 画像をダウンロード 
            $command = sprintf('curl %s -o %s', $img_src, $download_path);
            exec($command);



            // ローカル環境かどうかで画像の保存場所を変える
            if (IS_LOCAL) {
                // ローカルの場合
                $parent_path = ROOT_PATH.'/public_html/resources/images/cast/'.$parent_dir;
                $img_path = $parent_path.'/'.$img_name.'.jpg';

                // 親ディレクトリの確認
                if (! is_dir($parent_path)) {
                    mkdir($parent_path);
                    chmod($parent_path, 0777);
                }

                // 既にファイルがあるかどうかを確認
                if (! file_exists($img_path)) {
                    copy($download_path, $img_path);
                }

            } else {
                // リモートの場合
                $S3 = new S3();

                $response = $S3->get_object(
                    $S3::BUCKET,
                    'resources/images/cast/'.$parent_dir.'/'.$img_name.'.jpg'
                );

                if ($response->status == 404) {
                    // todo
                }


                $response = $S3->create_object(
                    $S3::BUCKET,
                    'resources/images/cast/'.$parent_dir.'/'.$img_name.'.jpg',
                    array(
                        'fileUpload' => $download_path
                    )
                );


                if (! $response->isOK()) {
                    echo 'プロフィール画像の保存に失敗しました！'.PHP_EOL;
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }



    /**
     * キャストデータをDBインサートする
     *
     * @return boolean
     **/
    public function insertCast ()
    {
        $cast_model = $this->container->get('CastModel');
        $cast = $cast_model->query->fetchByName($this->name);

        if (! $cast) {
            $params = new \stdClass;
            $params->cast_id = $this->cast_id;
            $params->dmm_name = $this->raw_name;
            $params->name = $this->name;
            $params->furigana = $this->furigana;
            $cast_model->insert($params);

            $this->cast = $cast_model->getRecord();


            // 新着情報を保管する
            if (! \Zend_Registry::isRegistered('cast')) {
                $data = array();
            } else {
                $data = \Zend_Registry::get('cast');
            }

            $data[] = array(
                'cast_id' => $cast_model->get('cast_id'),
                'dmm_name' => $cast_model->get('dmm_name'),
                'name' => $cast_model->get('name')
            );
            \Zend_Registry::set('cast', $data);
        }

        return true;
    }



    /**
     * EUC-JPからUTF-8へエンコードする
     *
     * @param string $string  エンコードする文字列
     * @author app2641
     **/
    private function _encode ($string)
    {
        return mb_convert_encoding($string, 'UTF-8', 'EUC-JP');
    }
}
