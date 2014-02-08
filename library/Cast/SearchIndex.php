<?php
/**
 * キャストの検索インデックスを生成する
 *
 * コンストラクタ引数にインデックスの保存先を指定して、
 * generate メソッドで渡したキャストモデルのインデックスを生成する
 **/


namespace Cast;

use Cast\Aws\S3;
use Cast\Model\CastModel;

class SearchIndex
{

    /**
     * 検索インデックス保存先定数
     *
     **/
    const LOCAL   = 'local';
    const DROPBOX = 'dropbox';
    const REMOTE  = 'remote';


    /**
     * @String
     *
     * 検索インデックスの保存先
     * local | dropbox | remote
     **/
    protected $destination;


    /**
     * @CastModel
     *
     * 検索インデックスを生成する対象のキャストモデル
     **/
    protected $model;


    
    public function __construct ($destination)
    {
        $this->destination = $destination;
    }



    /**
     * 引数に与えられるキャストモデルの検索インデックスを生成する
     * インデックスの保存先は $destination に依存する
     *
     * @param CastModel $model  対象のキャストモデル
     * @return void
     **/
    public function generate (CastModel $model)
    {
        try {
            mb_internal_encoding('UTF-8');

            $this->model = $model;
            $names = $model->decompositionName();
            
            foreach ($names as $name) {
                $name = strtolower($name);
                $path = $this->getDestinationPath($name);

                if ($this->destination == $this::REMOTE) {
                    // 既にresult.jsonがS3にあるかどうかを確認する
                    $S3 = new S3();

                    if ($S3->if_object_exists($S3::BUCKET, $path['json'])) {
                        $response = $S3->getSearchIndex($path['json']);
                        $json = json_decode($response->body);
                        $exists_names = array();

                        foreach ($json as $val) {
                            if (! in_array($val->value, $exists_names)) {
                                $exists_names[] = $val->value;
                            }
                        }

                        // 対象キャストをjson配列に格納する
                        if (! in_array($model->get('name'), $exists_names)) {
                            $json[] = array('value' => $model->get('name'));
                        }
                        $json = json_encode($json);

                    } else {
                        $json = json_encode(array(array('value' => $model->get('name'))));
                    }


                    // 検索インデックスをS3に保存する
                    $response = $S3->uploadSearchIndex($json, $path['json']);

                    if ($response->isOK()) {
                        // 検索インデックスフラグを更新する
                        $model->set('search_index', true);
                        $model->update();
                    }
                
                } else {
                    if (! is_dir($path['dir'])) {
                        mkdir($path['dir'], 0775, true);
                        chmod($path['dir'], 0775);
                    }


                    // 既に検索インデックスjsonが生成されているかどうか
                    if (file_exists($path['json'])) {
                        // 既存検索インデックスを取得
                        $json = json_decode(file_get_contents($path['json']));
                        $exists_names = array();

                        foreach ($json as $val) {
                            if (! in_array($val->value, $exists_names)) {
                                $exists_names[] = $val->value;
                            }
                        }

                        // 対象キャストをjson配列に格納する
                        if (! in_array($model->get('name'), $exists_names)) {
                            $json[] = array('value' => $model->get('name'));
                        }
                        $json = json_encode($json);

                    } else {
                        touch($path['json']);
                        chmod($path['json'], 0775);
                        $json = json_encode(array(array('value' => $model->get('name'))));
                    }

                    // jsonをファイルに保存
                    $fp = fopen($path['json'], 'w');
                    fwrite($fp, $json);
                    fclose($fp);
                    unset($fp);
                }
            }
        
        } catch (\Exception $e) {
            throw $e;
        }
    }



    /**
     * 保存先パスを取得する
     * dir 添字にはディレクトリを json 添字には json_path を格納する
     *
     * @param String $name  キャスト名
     * @return array
     **/
    public function getDestinationPath ($name)
    {
        $path = array();

        switch ($this->destination) {
            // MainCastローカル
            case $this::LOCAL:
                $path['dir'] = ROOT_PATH.'/public_html/resources/json/search';
                break;

            // Dropboxローカル
            case $this::DROPBOX:
                $user = explode('/', dirname(__FILE__))[2];
                $path['dir'] = '/Users/'.$user.'/Dropbox/Files/Share/MainCast/resources/json/search';
                break;

            // S3 リモート
            case $this::REMOTE:
                $path['dir'] = 'resources/json/search';
                break;

            // その他
            default:
                return false;
        }

        $length = mb_strlen($name);
        $path['dir']  = $path['dir'].'/'.$length.'/'.$name;
        $path['json'] = $path['dir'].'/result.json';

        return $path;
    }
}
