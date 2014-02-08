<?php

use Cast\Test\DatabaseTestCase;

use Cast\Container,
    Cast\Factory\ModelFactory;

use Cast\SearchIndex;

class SearchIndexTest extends DatabaseTestCase
{

    /**
     * @var Container
     *
     * ModelFactoryのコンテナクラス
     **/
    protected $container;


    public function setUp ()
    {
        parent::setUp();
        $this->container = new Container(new ModelFactory);
    }


    /**
     * インストラクタ生成時に引数がない例外テスト
     *
     * @expectedException Exception
     *
     * @group si
     * @group si-construct
     */
    public function testConstruct ()
    {
        $si = new SearchIndex();
    }



    /**
     * 検索インデックスローカル作成処理
     *
     * @group si
     * @group si-generate
     **/
    public function testGenerate ()
    {
        try {
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();

            $id = 1;
            $cast_model = $this->container->get('CastModel');
            $cast_model->fetchById($id);
        
            $si = new SearchIndex(SearchIndex::LOCAL);
            $si->generate($cast_model);
        
            $db->rollBack();
        
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }



    /**
     * 検索インデックスDropbox作成処理
     *
     * @group si
     * @group si-dropbox-generate
     **/
    public function testDropboxGenerate ()
    {
        try {
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();

            $id = 1;
            $cast_model = $this->container->get('CastModel');
            $cast_model->fetchById($id);
        
            $si = new SearchIndex(SearchIndex::DROPBOX);
            $si->generate($cast_model);
        
            $db->rollBack();
        
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }



    /**
     * 検索インデックスDropbox作成処理
     *
     * @group si
     * @group si-s3-generate
     **/
    public function testS3Generate ()
    {
        try {
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();

            $id = 1;
            $cast_model = $this->container->get('CastModel');
            $cast_model->fetchById($id);
        
            $si = new SearchIndex(SearchIndex::REMOTE);
            $result = $si->generate($cast_model);
        
            $db->rollBack();
        
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
