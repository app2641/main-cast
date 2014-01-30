<?php


use Cast\Test\DatabaseTestCase;
use Cast\Parser\Video;

class VideoTest extends DatabaseTestCase
{

    /**
     * @var String
     *
     * 解析する動画ページURL
     **/
    protected $url = 'http://www.dmm.co.jp/digital/videoa/-/detail/=/cid=53dv01566';



    public function setUp ()
    {
        parent::setUp();

        // IS_LOCAL定数を仕込む
        defined('IS_LOCAL') || define('IS_LOCAL', true);
    }



    /**
     * urlを指定しない場合のページ取得例外処理
     *
     * @expectedException Exception
     * @expectedExceptionMessage file_get_contents(): Filename cannot be empty
     *
     * @group video-page-exception
     * @group video
     */
    public function testNotSetUrlException ()
    {
        $video = new Video;
        $result = $video->parsePage();
    }



    /**
     * ページ取得処理テスト 
     *
     * @group video-page
     * @group video
     **/
    public function testParsePage ()
    {
        $video = new Video;
        $video->setUrl($this->url);

        $result = $video->parsePage();
        $this->assertTrue($result);
    }



    /**
     * キャスト解析処理テスト
     *
     * @group video-cast
     * @group video
     **/
    public function testParseCast ()
    {
        try {
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();
        
            $video = new Video;
            $video->setUrl($this->url);
            $video->parsePage();

            $result = $video->parseCast();
            $this->assertTrue(is_array($result));
        
            $db->rollBack();
        
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }



    /**
     * 動画タイトル取得テスト
     *
     * @group video-title
     * @group video
     */
    public function testParseTitle ()
    {
        $video = new Video;
        $video->setUrl($this->url);
        $video->parsePage();

        $result = $video->parseTitle();
        $this->assertTrue(is_string($result));
    }



    /**
     * パッケージ画像取得テスト
     *
     * @group video-image
     * @group video
     */
    public function testParseVideoImage ()
    {
        $video = new Video;
        $video->setUrl($this->url);
        $video->parsePage();

        $result = $video->parseVideoImage();
        $this->assertTrue($result);
    }



    /**
     * 動画情報解析処理
     *
     * @group video-execute
     * @group video
     */
    public function testExecute ()
    {
        try {
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();
        
            $video = new Video;
            $video->setUrl($this->url);

            $result = $video->execute();
            $this->assertTrue($result);
        
            $db->rollBack();
        
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
