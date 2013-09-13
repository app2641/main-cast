<?php


namespace Cast\Controller\Plugin;

use Cast\Auth\Authentication;

class Auth extends \Zend_Controller_Plugin_Abstract
{

    /**
     * Controolerのアクションを実行する前にエラーがないかを精査する
     *
     * @return Zend_Controller_Request_Abstract
     **/
    public function routeShutdown(\Zend_Controller_Request_Abstract $request)
    {
        if (! \Zend_Registry::isRegistered('error')) {
            $auth = new Authentication();
            \Zend_Registry::set('auth', $auth);

            if (! $auth->preAuth()) {
                \Zend_Registry::set('error', 'invalid_signin');
            }
        }

        return $request;
    }
}
