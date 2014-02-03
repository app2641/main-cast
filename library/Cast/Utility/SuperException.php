<?php


namespace Cast\Utility;

use Cast\Aws\SES;

class SuperException extends \Exception
{

    /**
     * Exception情報をメール送信する
     *
     * @param Exception $e  Exceptionクラス
     * @return void
     **/
    public static function mail (\Exception $e)
    {
        // Exceptionクラスからメール本文を構築する
        $body = $e->getMessage().PHP_EOL;
        $body .= $e->file.' on line '.$e->line.PHP_EOL.PHP_EOL;

        foreach ($e->getTrace() as $key => $trace) {
            // 引数の有無を確認
            if ($key == 0 && count($trace['args']) > 0) {
                $body .= 'Parameters:'.PHP_EOL;

                foreach ($trace['args'] as $args) {
                    foreach ($args as $key => $arg) {
                        $body .= $key.': '.$arg.PHP_EOL;
                    }
                }

                $body .= PHP_EOL;
            }


            // トレース内容を記述する
            $body .= $trace['file'].' on line '.$trace['line'].PHP_EOL;
            $body .= 'Class: '.$trace['class'].PHP_EOL;
            $body .= 'Function: '.$trace['function'].PHP_EOL;

            $body .= PHP_EOL;
        }


        $SES = new SES();
        $SES->mail($body);
    }
}
