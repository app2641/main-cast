<?php


namespace Cast\Model;

abstract class AbstractModel
{
    public $query;

    protected $record;



    /**
     * レコードの新規挿入
     *
     * @author app2641
     **/
    public final function insert (\stdClass $params)
    {
        $record = $this->query->insert($params);

        if ($record instanceof \stdClass) {
            $this->setRecord($record);
        }

        return $record;
    }



    /**
     * 指定レコードを更新する
     *
     * @author app2641
     **/
    public final function update ()
    {
        $this->query->update($this);
    }



    /**
     * 指定レコードを削除する
     *
     * @author app2641
     **/
    public final function delete ()
    {
        $this->query->delete($this);
    }



    /**
     * 指定idのレコードを内包レコードに挿入する
     *
     * @author app2641
     **/
    public final function fetchById ($id)
    {
        $record = $this->query->fetchById($id);

        if ($record instanceof \stdClass) {
            $this->setRecord($record);
        } else {
            $this->record = null;
        }

        return true;
    }



    /**
     * 内包レコードにオブジェクトをセットする
     *
     * @author app2641
     **/
    public final function setRecord (\stdClass $params)
    {
        // カラムクラスに登録されていないフィールドは削除する
        foreach ($params as $key => $param) {
            if (! in_array($key, $this->query->column->getColumns())) {
                unset($params->{$key});
            }
        }

        $this->record = $params;

        return true;
    }



    /**
     * 内包レコードを取得する
     *
     * @author app2641
     **/
    public final function getRecord ()
    {
        return $this->record;
    }



    /**
     * 内包レコードを所持しているかどうか
     *
     * @author app2641
     **/
    public final function hasRecord ()
    {
        return (is_null($this->record)) ? false: true;
    }



    /**
     * 指定フィールドの値を内包レコードから取得する
     *
     * @author app2641
     **/
    public final function get ($key)
    {
        if (in_array($key, $this->query->column->getColumns())) {
            return $this->record->{$key};
        }

        return false;
    }



    /**
     * 内包レコードの指定フィールドに値をセットする
     *
     * @author app2641
     **/
    public final function set ($key, $val)
    {
        if (in_array($key, $this->query->column->getColumns())) {
            $this->record->{$key} = $val;
            return true;
        }

        return false;
    }
}
