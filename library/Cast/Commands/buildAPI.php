<?php


namespace Cast\Commands;

class buildapi extends Base\AbstractCommand
{
    const DESCRIPTOR = 'CAST.REMOTING_API';
    const TYPE = 'remoting';
    const URL = '/direct.php';
    const TIMEOUT = 300000;


    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            $ini = new \Zend_Config_Ini(APPLICATION_PATH.DS.'configs'.DS.'core.ini', APPLICATION_ENV);
            $api = array();

            $module_path = APPLICATION_PATH.DS.'modules'.DS.'direct'.DS.'controllers';
            set_include_path($module_path . PATH_SEPARATOR . get_include_path());

            foreach ($ini->api->modules as $module) {
                $methods = get_class_methods($module);
                $api[$module] = array();

                foreach ($methods as $method) {
                    if (in_array($method, array('__construct'))) {
                        continue;
                    }

                    $ref = new \ReflectionMethod($module, $method);

                    if ($ref->isPublic()) {
                        $count = count($ref->getParameters());
                        $api[$module]['methods'][$method] = array('len' => $count);

                        $doc = $ref->getDocComment();

                        if (preg_match('/@formHandler/', $doc)) {
                            $api[$module]['methods'][$method]['formHandler'] = true;
                        }
                    }
                }
            }


            // APIファイルの生成
            $js = $this::DESCRIPTOR."={".
                "url:".$this->_addQuarto($this::URL).",".
                "type:".$this->_addQuarto($this::TYPE).",".
                "timeout:".$this->_addQuarto($this::TIMEOUT).",".
                "actions:{";

            foreach ($api as $module => $methods) {
                $js .= $module.":[";

                foreach ($methods['methods'] as $method => $values) {
                    $js .= "{name:".$this->_addQuarto($method).",len:".$values['len'];

                    if (isset($values['formHandler'])) {
                        $js .= ',formHandler:true';
                    }
                    
                    $js .= '},';
                }

                // 末尾のコンマを削除
                $js = preg_replace('/,$/', '', $js).'],';
            }

            // 末尾のコンマを削除
            $js = preg_replace('/,$/', '', $js).'}};';


            // API.jsの保存
            $path = ROOT_PATH.DS.'public_html'.DS.'resources'.DS.'js'.DS.'cast'.DS.'API.js';

            if (! file_exists($path)) {
                touch($path);
            }

            file_put_contents($path, $js);

            $this->log('generated API.js', 'success!');
        
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
        $msg = 'ExtDirect用のAPIファイルを生成する';

        return $msg;
    }



    /**
     * クオートを付与する
     *
     **/
    private function _addQuarto ($string)
    {
        return "'$string'";
    }
}
