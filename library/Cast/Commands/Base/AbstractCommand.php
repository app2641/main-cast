<?php


namespace Cast\Commands\Base;

use Cast\Database;

abstract class AbstractCommand 
{
    abstract function execute (Array $params);


    /**
     * コンソールに第一引数のメッセージを表示する
     * タイトルを付けたい場合には第二引数も指定する
     *
     * @author app2641
     **/
    public function log ($msg, $title = null)
    {
        if (is_null($title)) {
            $txt = '  ' .  pack('c',0x1B) . "[1m" . $msg . pack('c',0x1B) . "[0m" . "\n";

        } else {
            $txt = '  ' .  pack('c',0x1B) . "[1;32m" . $title . ':' . pack('c',0x1B) . "[0m" . '  ';
            $txt .= pack('c',0x1B) . "[1m" . $msg . pack('c',0x1B) . "[0m" . "\n";
        }

        echo $txt;
    }



    /**
     * コンソールにエラーメッセージを表示する
     *
     * @author app2641
     **/
    public function errorLog ($msg)
    {
        $txt = '  ' .  pack('c',0x1B) . "[1;31m" . $msg . pack('c',0x1B) . "[0m" . "\n";
        echo $txt;
    }



    /**
     * PDOインスタンスを生成してRegistryに登録する
     *
     * @author app2641
     **/
    public function initDatabaseConnection ()
    {
        if (! \Zend_Registry::isRegistered('db')) {
            $config   = new \Zend_Config_Ini(APPLICATION_PATH.'/configs/database.ini', 'database');
            $db_name  = $config->db->db;
            $host     = $config->db->host;
            $user     = $config->db->username;
            $password = $config->db->password;
            $dsn      = 'mysql:dbname='.$db_name.';host='.$host;

            $db = new Database($dsn, $user, $password);
            $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            \Zend_Registry::set('db', $db);
        }
    }
}
