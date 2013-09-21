<?php


namespace Cast\Parser;

require_once 'SimpleHtmlDomParser/simple_html_dom.php';

use Cast\Container,
    Cast\Factory\ModelFactory;

abstract class ParserAbstract
{

    /**
     * パースするページのURL
     *
     * @author app2641
     **/
    protected $url;



    /**
     * SimpleHtmlDomParserクラス
     *
     * @author app2641
     **/
    protected $html;



    /**
     * MainCast用野AmazonS3サブクラス
     *
     * @author app2641
     **/
    protected $S3;



    /**
     * ModelFactoryクラスを注入したCastContainerクラス
     *
     * @author app2641
     **/
    protected $container;



    public function __construct ()
    {
        $this->container = new Container(new ModelFactory);
    }



    /**
     * パースするURLをセットする
     *
     * @param string $url  パース対象のURL
     * @author app2641
     **/
    public function setUrl ($url)
    {
        $this->url = $url;
    }
}
