<?php


namespace Cast\Aws;

require_once 'AWSSDKforPHP/sdk.class.php';

class SES extends \AmazonSES
{

    /**
     * @var String
     *
     * 送信元メールアドレス
     **/
    protected $from = 'app2641+main-cast@gmail.com';



    /**
     * @var String
     *
     * 送信先メールアドレス
     **/
    protected $to = 'app2641+main-cast@gmail.com';




    public function __construct ()
    {
        $ini = new \Zend_Config_Ini(APPLICATION_PATH.'/configs/aws.ini', 'aws');

        parent::__construct(
            array(
                'key' => $ini->key,
                'secret' => $ini->secret
            )
        );

        $this->set_region(self::REGION_US_E1);
    }



    /**
     * SESメール送信処理
     *
     * @param String $body  メール本文
     * @param String $to  送信先アドレス
     * @return stdClass
     **/
    public function mail ($body, $to = false)
    {
        $to = ($to === false) ? $this->to: $to;

        $response = $this->send_email(
            $this->from,
            array('ToAddresses' => array($to)),
            array(
                'Subject.Data' => 'MainCast information mail',
                'Body.Text.Data' => $body
            )
        );

        return $response;
    }
}
