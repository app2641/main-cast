<?php


namespace Cast\Parser;

use Cast\Aws\S3;

class Video extends ParserAbstract implements ParserInterface
{

    /**
     * パースする動画ページのURL
     *
     * @author app2641
     **/
    protected $url;



    /**
     * 動画のタイトル
     *
     * @author app2641
     **/
    protected $title;



    /**
     * キャストのDMM用ID
     *
     * @param int
     **/
    protected $cast_id;



    /**
     * 解析するURLをセットする
     *
     * @param string $url  解析する動画ページのURL
     * @return void
     **/
    public function setUrl ($url)
    {
        $this->url = $url;
    }



    /**
     * ビデオタイトルを設定する
     *
     * @param string $title  ビデオタイトル
     * @author app2641
     **/
    public function setTitle ($title)
    {
        $this->title = $title;
    }

    

    /**
     * 解析処理
     *
     * @return boolean
     **/
    public function execute ()
    {
        try {
            set_time_limit(0);

            $db = \Zend_Registry::get('db');
            $db->beginTransaction();

            $this->parsePage();

            // キャスト情報を取得する
            $result = $this->parseCast();
            if ($result) {
                $this->parseVideoImage();
                $this->parseVideo();
            }

            $db->commit();
        
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return true;
    }



    /**
     * ページ解析処理
     *
     * @return boolean
     **/
    public function parsePage ()
    {
        try {

            $this->html = file_get_html($this->url);
            if ($this->html === false) {
                echo $this->url.PHP_EOL;
                throw new \Exception('動画ページを取得出来ませんでした');
            }
        
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }



    /**
     * Videoページからキャスト情報を解析する
     *
     * @return boolean
     **/
    public function parseCast ()
    {
        $cast_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr td span#performer a');

        if (count($cast_tag) != 1) {
            // 複数出演の場合は処理を中断する
            return false;
        }

        $cast_link = $cast_tag[0]->getAttribute('href');
        preg_match('/id=([0-9]*)/', $cast_link, $matches);
        if (! isset($matches[1])) {
            return false;
        }

        $this->cast_id = $matches[1];
        $cast_model = $this->container->get('CastModel');
        $casts = $cast_model->query->fetchAllByCastId($this->cast_id);

        if (count($casts) == 0) {
            // 未登録のキャストの場合
            $profile = new Profile();
            $profile->setCastId($this->cast_id);
            $profile->execute();
        }

        return true;
    }



    /**
     * 動画タイトルを取得する
     *
     * @return String
     **/
    public function parseTitle ()
    {
        try {
            $title = $this->html->find('div.page-detail div.hreview h1#title', 0)->plaintext;
            $this->title = $title;
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $title;
    }



    /**
     * 動画のパッケージ画像を取得する
     *
     * @return boolean
     **/
    public function parseVideoImage ()
    {
        // タイトルの取得
        if (is_null($this->title)) {
            $this->parseTitle();
        }


        $img_el = $this->html->find('div#sample-video a', 0);
        if (is_null($img_el)) {
            echo $this->url.PHP_EOL;
            throw new \Exception('パッケージ画像を取得できません！');
        }

        $img_src = $img_el->getAttribute('href');
        $img_name = md5($this->title);
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
            $parent_path = ROOT_PATH.'/public_html/resources/images/package/'.$parent_dir;
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

            $response = $S3->create_object(
                $S3::BUCKET,
                'resources/images/package/'.$parent_dir.'/'.$img_name.'.jpg',
                array(
                    'fileUpload' => $download_path
                )
            );


            if (! $response->isOK()) {
                echo 'パッケージ画像の保存に失敗しました！'.PHP_EOL;
            }
        }

        return true;
    }



    /**
     * Video情報を解析する
     *
     * @return boolean
     **/
    public function parseVideo ()
    {
        // descriptionの取得
        $description = trim($this->html->find('div.page-detail table tbody td div.lh4', 0)->plaintext);

        // 対応devideの取得
        $device = trim($this->html->find('div.page-detail table tbody tr td table tbody tr td', 1)->plaintext);

        // 発売日
        $date_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr', 2);
        $date = trim($date_tag->find('td', 1)->plaintext);

        // 収録時間
        $duration_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr', 3);
        $duration = trim($duration_tag->find('td', 1)->plaintext);

        // メーカー
        $maker_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr', 7);
        $maker = trim($maker_tag->find('td', 1)->plaintext);

        // レーベル
        $label_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr', 8);
        $label = trim($label_tag->find('td', 1)->plaintext);


        $params = new \stdClass;
        $params->cast_id = $this->cast_id;
        $params->title = $this->title;
        $params->description = $description;
        $params->device = $device;
        $params->duration = $duration;
        $params->sale_date = $date;
        $params->maker = $maker;
        $params->label = $label;
        $params->package = md5($this->title);
        $params->url = $this->url;

        $contents_model = $this->container->get('ContentsModel');
        $contents_model->insert($params);


        // 新着情報を保管する
        if (! \Zend_Registry::isRegistered('video')) {
            $data = array();
        } else {
            $data = \Zend_Registry::get('video');
        }

        $data[] = array(
            'title' => $this->title,
            'url' => $this->url
        );
        \Zend_Registry::set('video', $data);
    }
    
}
