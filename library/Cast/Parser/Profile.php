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
    protected $url;



    /**
     * キャスト情報を格納したstdClassクラス
     *
     * @author app2641
     **/
    protected $cast;



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



    public function execute ()
    {
        try {
            set_time_limit(0);
            $conn = \Zend_Registry::get('db');
            $conn->beginTransaction();
        
            $this->html = file_get_html($this->url);

            $this->getFurigana();
            $this->getImage();
            $this->InsertCast();
            
        
            $conn->commit();
        
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * キャスト名を設定する
     *
     * @param string $name  キャスト名
     * @author app2641
     **/
    public function setName ($name)
    {
        $this->name = $name;
    }



    /**
     * キャスト情報を取得する
     *
     * @author app2641
     **/
    public function getCast ()
    {
        return $this->cast;
    }



    /**
     * キャスト名のふりがなを解析して取得する
     *
     * @author app2641
     **/
    public function getFurigana ()
    {
        $cast_name = $this->_encode($this->html->find('table tbody tr td h1', 0)->plaintext);
        $furigana  = str_replace('）', '', str_replace($this->name.'（', '', $cast_name));
        $this->furigana = $furigana;
    }



    /**
     * プロフィール画像を取得して保存する
     *
     * @author app2641
     **/
    public function getImage ()
    {
        // 画像パスを取得
        $img_src = $this->html->find('table tbody tr td img[width="125"]', 0)->getAttribute('src');
        $img_name = md5($this->name);
        $download_path = '/tmp/cast/'.$img_name.'.jpg';

        if (! is_dir('/tmp/cast')) {
            mkdir('/tmp/cast');
            chmod('/tmp/cast', 0777);
        }


        // 画像をダウンロード 
        $command = sprintf('curl %s -o %s', $img_src, $download_path);
        exec($command);


        // ダウンロードした画像をS3へ保存
        $S3 = new S3();

        $parent_dir = substr($img_name, 0, 1);
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



    /**
     * キャストデータをDBインサートする
     *
     * @author app2641
     **/
    public function insertCast ()
    {
        $cast_model = $this->container->get('CastModel');
        $cast = $cast_model->query->fetchByName($this->name);

        if (! $cast) {
            $params = new \stdClass;
            $params->name = $this->name;
            $params->furigana = $this->furigana;
            $params->url = $this->url;
            $cast_model->insert($params);

            $cast = $cast_model->getRecord();


            // 新着情報を保管する
            if (! \Zend_Registry::isRegistered('cast')) {
                $data = array();
                \Zend_Registry::set('cast', $data);
            } else {
                $data = \Zend_Registry::get('cast');
            }

            $data[] = array(
                'name' => $this->name,
                'furigana' => $this->furigana,
                'url' => $this->url
            );
            \Zend_Registry::set('cast', $data);
        }

        $this->cast = $cast;
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
