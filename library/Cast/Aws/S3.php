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



    /**
     * 指定文字列の検索インデックスを取得する
     *
     * @param string $string  検索クエリ文字列
     * @author app2641
     **/
    public function getSearchIndex ($string)
    {
        mb_internal_encoding('UTF-8');

        $path = 'resources/json/search/'.mb_strlen($string).'/'.$string.'/result.json';

        $response = $this->get_object(
            $this::BUCKET,
            $path
        );

        return $response;
    }



    /**
     * 指定文字列の検索インデックスをS3に保存する
     *
     * @param string $string  検索クエリ文字列
     * @param array $json  検索インデックスの配列
     * @author app2641
     **/
    public function uploadSearchIndex ($string, $json)
    {
        mb_internal_encoding('UTF-8');

        $json = json_encode($json);
        $path = 'resources/json/search/'.mb_strlen($string).'/'.$string.'/result.json';

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
