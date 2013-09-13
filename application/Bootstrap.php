<?php

use Cast\Database,
    Cast\Auth;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public static $currentUser;

    /**
     * 基本設定
     *
     **/
    protected function _initBase()
    {
        defined('DS') || define('DS', DIRECTORY_SEPARATOR);

        // サイト名
        defined('SITE') || define('SITE', 'cast');

        // クッキー名
        defined('REMEMBER_KEY') || define('REMEMBER_KEY', 'cast_remember_key');
    }



    /**
     * ビューの初期化
     *
     **/
    protected function _initView()
    {
        $view = new Zend_View();

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
    }



    /**
     * データベースの初期化
     *
     **/
    protected function _initDatabase()
    {
        $config   = new \Zend_Config_Ini(APPLICATION_PATH.'/configs/database.ini', 'database');
        $db_name  = $config->db->db;
        $host     = $config->db->host;
        $user     = $config->db->username;
        $password = $config->db->password;
        $dsn      = 'mysql:dbname='.$db_name.';host='.$host;

        try {
            $db = new Database($dsn, $user, $password);
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            \Zend_Registry::set('db', $db);

        } catch (\PDOException $e) {
            if (! \Zend_Registry::isRegistered('error')) {
                \Zend_Registry::set('error', 'invalid_pdo');
            }
        }
    }



    /**
     * セッションの初期化
     *
     **/
    public function _initSession()
    {
        \Zend_Session::start();
    }
}
