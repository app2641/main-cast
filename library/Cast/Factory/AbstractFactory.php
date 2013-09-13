<?php


namespace Cast\Factory;

abstract class AbstractFactory
{
    protected $container;


    /**
     * 指定されたクラスをファクトリから抽出する
     *
     * @author app2641
     **/
    public function get ($cls_name)
    {
        return $this->{'build'.$cls_name}();
    }
}
