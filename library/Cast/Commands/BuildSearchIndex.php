<?php


namespace Cast\Commands;

use Cast\Container,
    Cast\Factory\ModelFactory;

use Cast\Aws\S3;
use Cast\SearchIndex;

class BuildSearchIndex extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            set_time_limit(0);

            if (! isset($params[1]) ||
                ! in_array($params[1], array(
                    SearchIndex::REMOTE,
                    SearchIndex::LOCAL,
                    SearchIndex::DROPBOX
                ))) {
                throw new \Exception('引数が不正です！');
            }


            $this->initDatabaseConnection();

            $si = new SearchIndex($params[1]);
        
            $container = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            $casts = $cast_model->query->notSearchIndexCasts($params[0]);

            foreach ($casts as $cast) {
                $cast_model->setRecord($cast);
                $si->generate($cast_model);
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
        $msg = 'キャストの検索インデックスデータを作成する。第一引数にremote指定でS3保存。その他はローカル保存。';

        return $msg;
    }
}
