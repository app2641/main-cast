<?php


namespace Cast\Commands;

class model extends Base\AbstractCommand
{
    protected
        $model_name,
        $app_name;


    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            if (! isset($params[0])) {
                throw new \Exception('モデル名を指定してください');
            }

            $this->model_name = ucfirst($params[0]);
            $this->app_name   = 'Cast';


            // Modelクラスの生成
            $skeleton = DATA.'/skeleton/ModelClassSkeleton.php';
            $model = ROOT_PATH.'/library/Cast/Model/'.$this->model_name.'Model.php';

            if (! file_exists($model)) {
                $data = file_get_contents($skeleton);
                $data = $this->_replaceData($data);
                file_put_contents($model, $data);
            }

            // Columnクラスの生成
            $skeleton = DATA.'/skeleton/ColumnClassSkeleton.php';
            $model = ROOT_PATH.'/library/Cast/Model/Column/'.$this->model_name.'Column.php';

            if (! file_exists($model)) {
                $data = file_get_contents($skeleton);
                $data = $this->_replaceData($data);
                file_put_contents($model, $data);
            }

            // Queryクラスの生成
            $skeleton = DATA.'/skeleton/QueryClassSkeleton.php';
            $model = ROOT_PATH.'/library/Cast/Model/Query/'.$this->model_name.'Query.php';

            if (! file_exists($model)) {
                $data = file_get_contents($skeleton);
                $data = $this->_replaceData($data);
                file_put_contents($model, $data);
            }


            $this->log('generated', 'success!');
        
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
        }
    }



    /**
     * データを置換する
     *
     * @author app2641
     **/
    private function _replaceData ($data)
    {
        $data = str_replace('Cast', $this->app_name, $data);
        $data = str_replace('{:Model}', $this->model_name, $data);

        return $data;
    }



    /**
     * コマンドリストに表示するヘルプメッセージを表示する
     *
     **/
    public static function help ()
    {
        /* write help message */
        $msg = '引数に指定したモデルクラスを生成します';

        return $msg;
    }
}
