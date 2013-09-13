<?php


namespace Cast\Factory;


/**
 * Modelクラス群
 *
 * @author app2641
 **/
use Cast\Model\CastModel,
    Cast\Model\ContentsModel;


/**
 * Queryクラス群
 *
 * @author app2641
 **/
use Cast\Model\Query\CastQuery,
    Cast\Model\Query\ContentsQuery;


/**
 * Columnクラス群
 *
 * @author app2641
 **/
use Cast\Model\Column\CastColumn,
    Cast\Model\Column\ContentsColumn;


class ModelFactory extends AbstractFactory
{
    
    /////////////////////
    // Model
    /////////////////////
    
    public function buildCastModel ()
    {
        return new CastModel;
    }



    public function buildContentsModel ()
    {
        return new ContentsModel;
    }
    



    /////////////////////
    // Query
    /////////////////////

    public function buildCastQuery ()
    {
        return new CastQuery;
    }



    public function buildContentsQuery ()
    {
        return new ContentsQuery;
    }
    



    /////////////////////
    // Column
    /////////////////////

    public function buildCastColumn ()
    {
        return new CastColumn;
    }



    public function buildContentsColumn ()
    {
        return new ContentsColumn;
    }
    
}
