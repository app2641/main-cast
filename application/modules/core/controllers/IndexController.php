<?php


use Cast\Container,
    Cast\Factory\ModelFactory;

class IndexController extends \Zend_Controller_Action
{

    public function init ()
    {
        if (APPLICATION_ENV == "development") {
            $debug = $this->getRequest()->getParam('debug');
            $this->view->debug = ($debug == "ct") ? true: false;

        } else {
            $this->view->debug = false;
        }
    }



    public function indexAction ()
    {
        //$this->_helper->viewRenderer->setNoRender();
    }



    public function editAction ()
    {
        $request = $this->getRequest();
        $this->view->cast_id = $request->getParam('cast_id');
    }



    /**
     * キャストページテンプレートをテストするアクション
     **/
    public function templateAction ()
    {
        $this->_helper->layout->disableLayout();


        $container  = new Container(new ModelFactory);
        $cast_model = $container->get('CastModel');
        $cast_model->fetchRandomCast();

        $contents_model = $container->get('ContentsModel');
        $contents = $contents_model->query->fetchAllByCastId($cast_model->get('cast_id'));

        $md_image   = md5($cast_model->get('name'));
        $cast_image = '/resources/images/cast/'.substr($md_image, 0, 1).'/'.$md_image.'.jpg';

        $this->view->cast     = $cast_model;
        $this->view->contents = $contents;
        $this->view->image    = $cast_image;
    }
}
