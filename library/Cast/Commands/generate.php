<?php


namespace Cast\Commands;

class generate extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     * @author app2641
     **/
    public function execute (Array $params)
    {
        try {
            if (! isset($params[0])) {
                throw new \Exception ('引数に作成するコマンド名を指定してください!');
            }


            // 生成するコマンドファイルのパス
            //$class_name = ucfirst($params[0]);
            $class_name = $params[0];
            $class_path = dirname(__FILE__).'/'.$class_name.'.php';

            if (file_exists($class_path)) {
                throw new \Exception(
                    sprintf('%s コマンドは既に作成されています！', $class_name)
                );
            }


            // スケルトンのパス
            $skeleton_path = DATA.DS.'Skeleton'.DS.'CommandClassSkeleton.php'; 

            $skeleton = file_get_contents($skeleton_path);
            $skeleton = str_replace('{$class}', $class_name, $skeleton);


            // コマンドファイルの生成
            touch($class_path);
            chmod($class_path, 0766);

            $fp = fopen($class_path, 'w');
            @fwrite($fp, $skeleton, strlen($skeleton));
            fclose($fp);


            $this->log('ganerate ' . $class_name . ' command!', 'success!');

        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }



    /**
     * コマンドリストに表示するヘルプメッセージを表示する
     *
     * @author app2641
     **/
    public static function help ()
    {
        /* write help message. */
        $msg = '新たなコマンドファイルを作成します。';

        return $msg;
    }
}
