<?php

class ErrorController extends Zend_Controller_Action
{

    public function init ()
    {
        $this->view->layout()->setLayout('error_layout', true);
    }



    /**
     * 何ならかのエラーが発生した場合
     * debugパラメータが付与されている場合にスタックトレースを行う
     *
     **/
    public function errorAction()
    {
        $errors = $this->getRequest()->getParam('error_handler');
        $this->view->errors = $errors;
        $this->view->exception = $errors->exception;


        // debug値の有無
        $debug_param = $this->getRequest()->getParam('debug');
        $debug = false;

        if (! is_null($debug_param) && $debug_param == "app") {
            $debug = true;
        }

        $this->view->debug = $debug;
    }
}

