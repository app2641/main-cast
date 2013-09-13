<?php


class DirectController extends \Zend_Controller_Action
{
    public function init ()
    {
        $module_path = APPLICATION_PATH.DS.'modules'.DS.'direct'.DS.'controllers';
        set_include_path($module_path . PATH_SEPARATOR . get_include_path());
    }


    public function indexAction ()
    {
        $this->_helper->viewRenderer->setNoRender();

        $is_form   = false;
        $is_upload = false;

        $raw_postdata = file_get_contents('php://input');
        $data = json_decode($raw_postdata);


        if (! is_null($data)) {
            header('Content-Type: text/javascript');
        
        } elseif (isset($_POST['extAction'])) {
            $is_form      = true;
            $is_upload    = $_POST['extUpload'] == 'true';
            $data         = new stdClass();
            $data->action = $_POST['extAction'];
            $data->method = $_POST['extMethod'];
            $data->tid    = isset($_POST['extTID']) ? $_POST['extTID']: null;
            $data->data   = array($_POST, $_FILES);
        
        } else {
            echo 'invalid request!';
            return false;
        }


        if (is_array($data)) {
            $response = array();
            foreach($data as $d){
                $response[] = $this->_doRpc($d);
            }
        } else {
            $response = $this->_doRpc($data);
        }


        if ($is_form && $is_upload) {
            $json = json_encode($response);

            echo '<html><body><textarea>';
            echo $json;
            echo '</textarea></body></html>';

        } else {
            echo json_encode($response);
        }
    }



    private function _doRpc ($cdata)
    {
        try {
            $action = $cdata->action;
            $method = $cdata->method;
            $priv   = $action.'-'.$method;

            $response = array(
                'type'   => 'rpc',
                'tid'    => $cdata->tid,
                'action' => $action,
                'method' => $method
            );


            $object = new $action();
            $params = isset($cdata->data) && is_array($cdata->data) ? $cdata->data : array();

            $response['result'] = call_user_func_array(array($object, $method), $params);

        } catch (Exception $e) {
            $response['type']    = 'exception';
            $response['message'] = 'ERROR!';

            //Err::invoke($e);
        }

        return $response;
    }
}
