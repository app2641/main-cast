<?php


namespace Cast\Commands;

class {$class} extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            /* write command action */
        
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
        $msg = '';

        return $msg;
    }
}
