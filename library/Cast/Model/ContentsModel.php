<?php


namespace Cast\Model;

use Cast\Container,
    Cast\Factory\ModelFactory;

class ContentsModel extends AbstractModel
{
    public $query;


    public function __construct ()
    {
        $container = new Container(new ModelFactory);
        $this->query = $container->get('ContentsQuery');
    }
}
