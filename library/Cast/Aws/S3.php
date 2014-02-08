<?php


namespace Cast\Aws;

require_once 'AWSSDKforPHP/sdk.class.php';

class S3 extends \AmazonS3
{
    const BUCKET = 'maincast.adult-midnight.net';


    public function __construct ()
    {
        $ini = new \Zend_Config_Ini(APPLICATION_PATH.'/configs/aws.ini', 'aws');

        parent::__construct(
            array(
                'key' => $ini->key,
                'secret' => $ini->secret
            )
        );

        $this->set_region(self::REGION_APAC_NE1);
    }



    /**
     * キャスト画像をS3へ保存する
     *
     * @param string $type  アップロード先 (cast|package)
     * @param string $img  キャスト画像のファイル名
     * @author app2641
     **/
    public function uploadImage ($type, $img)
    {
        try {
            $parent = substr($img, 0, 1);
            $path = ROOT_PATH.'/public_html/resources/images/'.$type.'/'.$parent.'/'.$img;

            if (! file_exists($path)) {
                throw new \Exception($path.' ファイルが見つかりません');
            }


            $response = $this->create_object(
                $this::BUCKET,
                'resources/images/'.$type.'/'.$parent.'/'.$img,
                array(
                    'fileUpload' => $path
                )
            );

            if (! $response->isOK()) {
                throw new \Exception($type.'画像のS3アップロードに失敗しました');
            } else {
                echo $img.' uploaded!'.PHP_EOL;
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }



    /**
     * 指定文字列の検索インデックスを取得する
     *
     * @param string $path  保存先パス
     * @author app2641
     **/
    public function getSearchIndex ($path)
    {
        mb_internal_encoding('UTF-8');

        $response = $this->get_object(
            $this::BUCKET,
            $path
        );

        return $response;
    }



    /**
     * 指定文字列の検索インデックスをS3に保存する
     *
     * @param array $json  検索インデックスJson
     * @param string $path  保存先パス
     * @author app2641
     **/
    public function uploadSearchIndex ($json, $path)
    {
        mb_internal_encoding('UTF-8');

        $response = $this->create_object(
            $this::BUCKET,
            $path,
            array(
                'body' => $json
            )
        );

        return $response;
    }
}
