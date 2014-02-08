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

            if (isset($params[0]) && $params[0] == 'remote') {
                $isAWS = true;
            } else {
                $isAWS = false;
            }


            $this->initDatabaseConnection();

            $S3 = new S3();
        
            $container = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            $casts = $cast_model->query->notSearchIndexCasts();

            foreach ($casts as $cast) {
                $cast_model->setRecord($cast);

                // キャスト名、ふりがなを解析する
                $values = $cast_model->decompositionValue();


                // 検索情報を保存する
                foreach ($values as $value) {
                    try {
                        $db = \Zend_Registry::get('db');
                        $db->beginTransaction();
                    

                        if ($isAWS) {
                            // 既にresult.jsonがS3にあるかどうかを確認する
                            $response = $S3->getSearchIndex($value, $path['json']);

                            if ($response->status == 200) {
                                $json = json_decode($response->body);
                                $names = array();

                                foreach ($json as $val) {
                                    if (! in_array($val->name, $names)) {
                                        $names[] = $val->name;
                                    }
                                }

                                if (! in_array($value, $names)) {
                                    $json[] = array('name' => $value);
                                }

                                $json = json_encode($json);

                            } else {
                                $json = array($cast->name);
                            }


                            // 検索インデックスをS3に保存する
                            $response = $S3->uploadSearchIndex($value, $json);

                            if ($response->isOK()) {
                                $cast_model->set('search_index', true);
                                $cast_model->update();
                            }


                        } else {
                            // ローカル保存の場合
                            mb_internal_encoding('UTF-8');

                            $length = mb_strlen($value);
                            $json_path = ROOT_PATH.'/resources/json/search/'.$length.'/'.$value;
                            if (! is_dir($json_path)) {
                                mkdir($json_path, 0755, true);
                            }


                            $json_path .= '/result.json';
                            if (file_exists($json_path)) {
                                $json = json_decode(file_get_contents($json_path));
                                $names = array();

                                foreach ($json as $val) {
                                    if (! in_array($val->name, $names)) {
                                        $names[] = $val->name;
                                    }
                                }

                                if (! in_array($cast_model->get('name'), $names)) {
                                    $json[] = array('name' => $cast_model->get('name'));
                                }
                                $json = json_encode($json);
                            
                            } else {
                                touch($json_path);
                                $json = json_encode(array(array('name' => $cast_model->get('name'))));
                            }

                            $fp = fopen($json_path, 'w');
                            fwrite($fp, $json);
                            fclose($fp);
                            unset($fp);
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
