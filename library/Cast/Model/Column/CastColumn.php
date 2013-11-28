<?php


namespace Cast\Model\Column;

class CastColumn implements ColumnInterface
{
    protected
        $columns = array(
            'id',
            'cast_id',
            'dmm_name',
            'name',
            'furigana',
            'search_index',
            'contents_index',
            'is_active'
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
