<?php


namespace Cast\Parser;

use Cast\Aws\S3;

class Video extends ParserAbstract implements ParserInterface
{

    /**
     * パースするビデオページのURL
     *
     * @author app2641
     **/
    protected $url;



    /**
     * ビデオのタイトル
     *
     * @author app2641
     **/
    protected $title;



    /**
     * キャスト情報を格納したstdClass
     *
     * @author app2641
     **/
    protected $cast = false;

    
    public function execute ()
    {
        try {
            set_time_limit(0);

            $conn = \Zend_Registry::get('db');
            $conn->beginTransaction();

            $this->html = file_get_html($this->url);
            $this->cast = $this->parseCast();

            if ($this->cast !== false) {
                $this->parseVideo();
            }

            $conn->commit();
        
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * Videoページからキャスト情報を解析する
     *
     * @author app2641
     **/
    public function parseCast ()
    {
        $cast_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr td span#performer a');

        if (count($cast_tag) != 1) {
            // 複数出演の場合は処理を中断する
            return false;
        }

        $cast_name = $cast_tag[0]->plaintext;
        $cast_model = $this->container->get('CastModel');
        $cast = $cast_model->query->fetchByName($cast_name);

        if (! $cast) {
            // 新人の場合はプロフィールページの解析も行う
            // AV女優IDはaタグのhrefから拾う
            $url = $cast_tag[0]->getAttribute('href');
            $url = str_replace('/', '', $url);
            preg_match('/id=([0-9]*)/', $url, $matches);
            $id = $matches[1];

            $profile = new Profile();
            $profile->setUrl(sprintf('http://actress.dmm.co.jp/-/detail/=/actress_id=%s', $id));
            $profile->setName($cast_name);
            $profile->execute();

            $cast = $profile->getCast();

            if ($cast == false) {
                // 解析失敗
                return false;
            }
        }

        return $cast;
    }



    /**
     * Video情報を解析する
     *
     * @author app2641
     **/
    public function parseVideo ()
    {
        // 既に登録済みのVideoかどうかを確認する
        $contents_model = $this->container->get('ContentsModel');
        $contents = $contents_model->query->fetchByTitleWithCastId($this->title, $this->cast->id);

        if ($contents) {
            return false;
        }


        $this->html = file_get_html($this->url);

        // 画像パスを取得
        $img_src = $this->html->find('div#sample-video a', 0)->getAttribute('href');
        $img_name = md5($this->title);
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
            'resources/images/contents/'.$parent_dir.'/'.$img_name.'.jpg',
            array(
                'fileUpload' => $download_path
            )
        );

        if (! $response->isOK()) {
            echo 'Video画像の保存に失敗しました！'.PHP_EOL;
        }


        // descriptionの取得
        $description = trim($this->html->find('div.page-detail table tbody td div.lh4', 0)->plaintext);

        // 対応devideの取得
        $device = $this->html->find('div.page-detail table tbody tr td table tbody tr td', 1)->plaintext;

        // 発売日
        $date_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr', 2);
        $date = $date_tag->find('td', 1)->plaintext;

        // 収録時間
        $duration_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr', 3);
        $duration = $duration_tag->find('td', 1)->plaintext;

        // メーカー
        $maker_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr', 7);
        $maker = $maker_tag->find('td', 1)->plaintext;

        // レーベル
        $label_tag = $this->html->find('div.page-detail table tbody tr td table tbody tr', 8);
        $label = $label_tag->find('td', 1)->plaintext;


        $params = new \stdClass;
        $params->cast_id = $this->cast->id;
        $params->title = $this->title;
        $params->description = $description;
        $params->device = $device;
        $params->duration = $duration;
        $params->sale_date = $date;
        $params->maker = $maker;
        $params->label = $label;
        $params->package = md5($this->title);
        $params->url = $this->url;

        $contents_model->insert($params);


        // 新着情報を保管する
        if (! \Zend_Registry::isRegistered('video')) {
            $data = array();
             \Zend_Registry::set('video', $data);
        } else {
            $data = \Zend_Registry::get('video');
        }

        $data[] = array(
            'title' => $this->title,
            'cast' => $this->cast->name,
            'url' => $this->url
        );
        \Zend_Registry::set('video', $data);
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
}
