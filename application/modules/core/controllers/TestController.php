<?php


class TestController extends \Zend_Controller_Action
{

    public function init ()
    {
        if (APPLICATION_ENV != 'development') {
            $this->_redirect('/');
        }
    }


    public function indexAction ()
    {
        $this->view->layout()->disableLayout();
        $this->view->site = SITE;
    }



    public function listAction ()
    {
        $query = strtolower($this->getRequest()->getParam('query'));

        $path = ROOT_PATH.DS.'public_html'.DS.'ct'.DS.'ct.json';
        $data = json_decode(file_get_contents($path));

        if ($query) {
            $result = array();
            foreach ($data->items as $d) {
                foreach ($d as $v) {
                    if (mb_strpos(strtolower($v), $query) !== false) {
                        $result[] = $d;
                        break;
                    }
                }
            }
            $data->items = $result;
        }

        $this->_helper->json($data);
    }



    public function cmpAction ()
    {
        $this->view->layout()->disableLayout();

        $c = $this->getRequest()->getParam('c');
        $cls = $this->getRequest()->getParam('cls');
        $ex = explode('.', str_replace('CAST.view.', '', $cls));
        $path = '/ct/' . strtolower($c) . '/' . $ex[0] . '/ct_' . $ex[1] . '.js';

        $this->view->cls = $cls;
        $this->view->path = $path;
        $this->view->site = SITE;
    }



    public function appAction ()
    {
        $this->view->layout()->disableLayout();
        $this->view->site = SITE;
    }
}
