<?php


namespace Cast\Model\Column;

class ContentsColumn implements ColumnInterface
{
    protected
        $columns = array(
            'id',
            'cast_id',
            'title',
            'description',
            'device',
            'duration',
            'sale_date',
            'maker',
            'label',
            'package',
            'url'
        );


    /**
     * テーブルのカラム情報を取得する
     *
     * @author app2641
     **/
    public function getColumns ()
    {
        return $this->columns;
    }
}
