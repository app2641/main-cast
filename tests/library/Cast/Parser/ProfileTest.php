<?php


use Cast\Test\DatabaseTestCase;
use Cast\Parser\Profile;

class ProfileTest extends DatabaseTestCase
{

    /**
     * @int
     *
     * テスト用キャストID
     **/
    protected $cast_id = 1020495;



    public function setUp ()
    {
        parent::setUp();

        // IS_LOCAL定数を仕込む
        defined('IS_LOCAL') || define('IS_LOCAL', true);
    }



    /**
     * キャストID未指定によるページ取得例外処理
     *
     * @expectedException Exception
     * @expectedExceptionMessage DMM用のキャストIDが指定されていません！
     *
     * @group profile-page-exception
     * @group profile
     */
    public function testParsePageException ()
    {
        $profile = new Profile;
        $profile->parsePage();
    }



    /**
     * ページ取得テスト
     *
     * @group profile-page 
     * @group profile
     */
    public function testParsePage ()
    {
        $profile = new Profile;
        $profile->setCastId($this->cast_id);
        $result = $profile->parsePage();

        $this->assertTrue($result);
    }



    /**
     * 名前取得テスト
     *
     * @group profile-name
     * @group profile
     **/
    public function testParseName ()
    {
        $profile = new Profile;
        $profile->setCastId($this->cast_id);
        $profile->parsePage();

        $name = $profile->parseName();
        $this->assertTrue(is_string($name));
    }



    /**
     * ふりがな取得テスト
     *
     * @group profile-furigana
     * @group profile
     **/
    public function testParseFurigana ()
    {
        $profile = new Profile;
        $profile->setCastId($this->cast_id);
        $profile->parsePage();
        $profile->parseName();

        $result = $profile->parseFurigana();
        $this->assertTrue(is_string($result));
    }



    /**
     * キャスト画像取得テスト
     *
     * @group profile-image
     * @group profile
     **/
    public function testParseCastImage ()
    {
        $profile = new Profile;
        $profile->setCastId($this->cast_id);
        $profile->parsePage();
        $profile->parseName();
        $profile->parseFurigana();

        $result = $profile->parseCastImage();
        $this->assertTrue($result);
    }



    /**
     * キャスト情報DBインサートテスト
     *
     * @group profile-insert
     * @group profile
     */
    public function testInsertCast ()
    {
        try {
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();

            $profile = new Profile;
            $profile->setCastId($this->cast_id);
            $profile->parsePage();
            $profile->parseName();
            $profile->parseFurigana();
            $profile->parseCastImage();

            $result = $profile->insertCast();
            $this->assertTrue($result);

            $db->rollBack();
        
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
