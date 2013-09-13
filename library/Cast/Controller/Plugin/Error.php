<?php


namespace Cast\Controller\Plugin;

class Error extends \Zend_Controller_Plugin_Abstract
{

    /**
     * Controolerのアクションを実行する前にエラーがないかを精査する
     *
     * @return Zend_Controller_Request_Abstract
     **/
    public function routeShutdown(\Zend_Controller_Request_Abstract $request)
    {
        // エラーが発生しているかを精査する
        if (\Zend_Registry::isRegistered('error')) {
            $error = \Zend_Registry::get('error');

            $controller = $this->getRequest()->getControllerName();
            $action     = $this->getRequest()->getActionName();

            if ($controller != 'direct') {
                switch ($error) {
                case 'invalid_signin':
                    if ($controller != 'auth' && $action != 'login') {
                        $this->getRequest()->setControllerName('auth');
                        $this->getRequest()->setActionName('login');
                    }
                    break;
                }

            } else {
                $json = json_decode($this->getRequest()->getRawBody());

                if (is_null($json)) {
                    return $request;

                } else {
                    // 許可されたメソッド群をクラス名をキーに配列で指定する
                    $enable_methods = array(
                        'User' => array(
                            'login' => true
                        )
                    );

                    if (! isset($enable_methods[$json->action][$json->method])) {
                        // 許可されていないメソッドの場合にthrow
                        throw new \Exception('Permission denied!');
                    }
                }
            }
        }

        return $request;
    }
}
