<?php


namespace Cast\Commands;

class bind extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            if (! isset($params[0])) {
                throw new \Exception('第一引数にバインドするファイル名を指定してください');
            }

            $bind_path = ROOT_PATH.DS.'library'.DS.'bind';
            $file = $params[0];

            if (! file_exists($bind_path.DS.$file.'.yaml')) {
                throw new \Exception('指定した名前のYAMLファイルが存在しません');
            }

            $command = 'java -jar '.$bind_path.DS.'compiler.jar ';


            // YAMLファイルを読み込んでコマンドに追記していく
            $yaml = new \Zend_Config_Yaml($bind_path.DS.$file.'.yaml');

            foreach ($yaml->src as $js) {
                $command .= '--js '.$yaml->path.$js.' ';
            }

            $command .= '--js_output_file ' . $yaml->dest;

            passthru($command);
        
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
        $msg = '第一引数に指定するlibrary/bind/$params[0].yamlのJSをバインドする';

        return $msg;
    }
}
