<?php


namespace Cast\Commands;

class test extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {

            $command = 'clear; phpunit --configuration tests/phpunit.xml';

            // 引数の有無
            if (isset($params[0])) {
                $command .= ' --group '.$params[0];
            }


            exec($command, $output);

            foreach ($output as $o) {
                $this->log($o);
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
        $msg = 'テストを実行する';

        return $msg;
    }
}
