<?php


namespace Cast\Commands;

use Cast\Aws\S3;

class Upload extends Base\AbstractCommand
{
    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            set_time_limit(0);

            $cast_path = ROOT_PATH.'/public_html/resources/image/cast';
            $S3 = new S3();


            // imageを順に取得する
            if ($dh = opendir($cast_path)) {
                while ($img = readdir($dh)) {
                    if (preg_match('/\.jpg/', $img)) {
                        $S3->uploadCastImage($img);
                    }
                }
                closedir($dh);
            }
        
        } catch (\Exception $e) {
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
        $msg = 'キャスト画像をS3にアップロードする';

        return $msg;
    }
}
