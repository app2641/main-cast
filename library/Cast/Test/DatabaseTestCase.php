<?php


namespace Cast\Test;

use Cast\Database,
    Cast\Auth\Authentication;

use Cast\Container,
    Cast\Factory\ModelFactory;

class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    protected
        $application,
        $container;


    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection ()
    {
        try {
            if (! \Zend_Registry::isRegistered('db')) {
                $config   = new \Zend_Config_Ini(APPLICATION_PATH.'/configs/database.ini', 'test');
                $db_name  = $config->db->db;
                $host     = $config->db->host;
                $user     = $config->db->username;
                $password = $config->db->password;
                $dsn      = 'mysql:dbname='.$db_name.';host='.$host;

                $db = new Database($dsn, $user, $password);
                $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                \Zend_Registry::set('db', $db);
            } else {
                $db = \Zend_Registry::get('db');
            }

        } catch (\PDOException $e) {
            $msg = $e->getMessage().PHP_EOL.PHP_EOL;

            $msg .= 'データベースのテストにはテスト用DBが必要です！'.PHP_EOL;
            $msg .= 'data/fixture/tests_schema.dbからデータベースを作成してください！';

            throw new \Exception($msg);
        }

        return $this->createDefaultDBConnection($db);
    }



    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet ()
    {
        $ds = $this->createFlatXmlDataSet(ROOT_PATH.'/data/fixtures/tests.xml');
        $rds = new \PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($ds);
        $rds->addFullReplacement('##null##', null);

        return $rds;
    }



    /**
     * @return void
     * 初期化処理を記載する
     */
    public function setUp ()
    {
        parent::setUp();


        $this->application = new \Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/core.ini'
        );

        $this->application->bootstrap();


        $this->container = new Container(new ModelFactory);
        $user_table = $this->container->get('UserTable');
        $user_id = 1;
        $user = $user_table->fetchById($user_id);

        // authの登録
        $auth = new Authentication();
        $auth->setStorage($user);
        \Zend_Registry::set('auth', $auth);
    }
}
