<?php


use Cast\Test\DatabaseTestCase;
use Cast\Parser\Feed;

class FeedTest extends DatabaseTestCase
{

    /**
     * @Cast\Parser\Feed;
     *
     **/
    protected $feed;



    public function setUp ()
    {
        parent::setUp();
        $this->feed = new Feed();
    }



    /**
     * execute メソッドテスト
     *
     * @group feed
     */
    public function testExecute ()
    {
        try {
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();

            $this->feed->execute();

            $db->rollBack();
        
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
