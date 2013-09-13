<?php


namespace Cast\Commands;

class compass extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            /* write command action */
            if (! isset($params[0])) {
                throw new \Exception('引数にsassファイル名を指定してください！');
            }


            // 引数に指定したsassファイルがあるかどうか
            $sass_path = ROOT_PATH.DS.'public_html'.DS.'resources'.DS.'sass';

            if ((!file_exists($sass_path.DS.$params[0].'.scss')) && (!file_exists($sass_path.DS.$params[0]))) {
                throw new \Exception(sprintf('%s.scssというファイルは存在しません！', str_replace('.scss', '', $params[0])));
            }


            $path = 'public_html'.DS.'resources'.DS.'sass';
            chdir($path);

            $cmd = 'compass compile ' . str_replace('.scss', '', $params[0]) . '.scss';

            try {
                passthru($cmd);
                
            } catch (\RunTimeException $runtime_exp) {
                throw $runtime_exp;
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
        $msg = '引数に指定したsassファイルをコンパイルする';

        return $msg;
    }
}
