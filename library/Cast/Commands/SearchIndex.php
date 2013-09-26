<?php


namespace Cast\Commands;

use Cast\Container,
    Cast\Factory\ModelFactory;

use Cast\Aws\S3;

class SearchIndex extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            set_time_limit(0);

            mb_internal_encoding('UTF-8');
            $this->initDatabaseConnection();

            $S3 = new S3();
        
            $container = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            $casts = $cast_model->query->notSearchIndexCasts();

            foreach ($casts as $cast) {
                // キャスト名を分解する
                $names = $this->_decompositionValue($cast->name);

                // ふりがなを分解する
                $furiganas = $this->_decompositionValue($cast->furigana);

                $values = array_merge($names, $furiganas);


                // 検索情報をS3に保存する
                foreach ($values as $value) {
                    try {
                        $db = \Zend_Registry::get('db');
                        $db->beginTransaction();
                    
                        // 既にresult.jsonがS3にあるかどうかを確認する
                        $response = $S3->getSearchIndex($value);

                        if ($response->status == 200) {
                            $json = json_decode($response->body);

                            if (! in_array($cast->name, $json)) {
                                $json[] = $cast->name;
                            }

                        } else {
                            $json = array($cast->name);
                        }


                        // 検索インデックスをS3に保存する
                        $response = $S3->uploadSearchIndex($value, $json);

                        if ($response->isOK()) {
                            $cast_model->setRecord($cast);
                            $cast_model->set('search_index', true);
                            $cast_model->update();
                        }
                        
                        $db->commit();
                    
                    } catch (\Exception $e) {
                        $db->rollBack();
                        $this->errorLog($e->getMessage());
                    }
                }
            }
        
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }



    /**
     * 引数の文字列をそれぞれに分解して配列で返す
     *
     * @param string $value  分解する文字列
     * @author app2641
     **/
    private function _decompositionValue ($value)
    {
        $data = array();
        $count = mb_strlen($value);

        // 最低文字列2から文字数分ループ処理を行う 
        for ($i = 2; $i <= $count; $i++) {

            // substr始点から$i分の文字列を切り取る
            for ($r = 0; $r <= ($count - $i); $r++) {
                $data[] = mb_substr($value, $r, $i, 'UTF-8');
            }
        }

        return $data;
    }



    /**
     * コマンドリストに表示するヘルプメッセージを表示する
     *
     **/
    public static function help ()
    {
        /* write help message */
        $msg = 'キャストの検索インデックスデータを作成する';

        return $msg;
    }
}
