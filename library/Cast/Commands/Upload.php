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

            if (! isset($params[0])) {
                throw new \Exception('引数を指定してください');
            } elseif (! in_array($params[0], array('cast', 'package'))) {
                throw new \Exception('引数にはcastかpackageを指定してください');
            }


            $type = $params[0];
            $cast_path = ROOT_PATH.'/public_html/resources/images/'.$type;
            $dir = explode(',', '0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f');
            $S3 = new S3();


            // 親ディレクトリを反復取得
            foreach ($dir as $dir) {
                if ($dh = opendir($cast_path.'/'.$dir)) {
                    while ($img = readdir($dh)) {
                        if (preg_match('/\.jpg/', $img)) {
                            $S3->uploadImage($type, $img);
                        }
                    }
                    closedir($dh);
                }
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
        $msg = '引数(cast|package)の画像をS3にアップロードする';

        return $msg;
    }
}
