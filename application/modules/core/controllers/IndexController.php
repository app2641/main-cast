<?php


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
}
