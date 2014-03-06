<?php


namespace Cast\Commands;

class Slim extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            /* write command action */
            $slim_path = DATA.'/skeleton/CastPage.slim';
            $temp_path = APPLICATION_PATH.'/modules/core/views/scripts/index/template.phtml';


            if (isset($params[0]) && $params[0] == true) {
                $option = '-p';
            } else {
                $option = '';
            }

            $command = sprintf(
                'slimrb %s %s > %s',
                $option, $slim_path, $temp_path
            );
            exec($command, $result, $output);
            var_dump($output);
            exit();

            $this->log('success!');
        
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
        $msg = 'data/skeleton/CastPage.slim からテンプレートページを生成する';

        return $msg;
    }
}
