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
     * @param string $img  キャスト画像のファイル名
     * @author app2641
     **/
    public function uploadCastImage ($img)
    {
        try {
            $path = ROOT_PATH.'/public_html/resources/image/cast/'.$img;
            $parent = substr($img, 0, 1);

            if (! file_exists($path)) {
                throw new \Exception($path.' ファイルが見つかりません');
            }


            $response = $this->create_object(
                $this::BUCKET,
                'resources/images/cast/'.$parent.'/'.$img,
                array(
                    'fileUpload' => $path
                )
            );

            if (! $response->isOK()) {
                throw new \Exception('キャスト画像のS3アップロードに失敗しました');
            }


            // アップロードしたファイルを親フォルダ管理下に移動させる
            $parent_dir = ROOT_PATH.'/public_html/resources/image/cast/'.$parent;
            if (! is_dir($parent_dir)) {
                mkdir($parent_dir);
            }

            $new_path = $parent_dir.'/'.$img;
            rename($path, $new_path);
        
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
