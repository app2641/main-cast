<?php


namespace Cast\Commands;

use Cast\Container,
    Cast\Factory\ModelFactory;

class ImportMovies extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            set_time_limit(0);

            $this->initDatabaseConnection();
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();

            if (! isset($params[0])) {
                throw new \Exception('頭文字を指定してください');
            }

            $container  = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            // 指定頭文字のキャストを取得する
            $casts = $cast_model->query->fetchAllByInitial($params[0]);
            var_dump(count($casts));
            exit();
            foreach ($casts as $cast) {
                
            }

            $db->commit();
        
        } catch (\Exception $e) {
            $db->rollBack();
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
        $msg = '動画情報をDMMから取得する';

        return $msg;
    }
}
