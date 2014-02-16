<?php


namespace Cast\Model\Query;

use Cast\Container,
    Cast\Factory\ModelFactory;

use Cast\Model\AbstractModel;

class ContentsQuery implements QueryInterface
{
    protected $db;

    public $column;


    public function __construct ()
    {
        $this->db = \Zend_Registry::get('db');

        $container = new Container(new ModelFactory);
        $this->column = $container->get('ContentsColumn');
    }



    /**
     * レコードを新規作成する
     *
     * @author app2641
     **/
    public final function insert (\stdClass $params)
    {
        try {
            foreach ($params as $key => $val) {
                if (! in_array($key, $this->column->getColumns())) {
                    throw new \Exception('invalid column '.$key);
                }
            }

            $sql = 'INSERT INTO contents
                (cast_id, title, description, device,
                    duration, sale_date, maker, label, package, url)
                VALUES (:cast_id, :title, :description, :device,
                    :duration, :sale_date, :maker, :label, :package, :url)';

            $this->db->state($sql, $params);

        } catch (\Exception $e) {
            throw $e;
        }

        return $this->fetchById($this->db->lastInsertId());
    }



    /**
     * レコードを更新する
     *
     * @author app2641
     **/
    public final function update (AbstractModel $model)
    {
        try {
            $record = $model->getRecord();

            foreach ($record as $key => $val) {
                if (! in_array($key, $this->column->getColumns())) {
                    throw new \Exception('invalid column!');
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }



    /**
     * レコードを削除する
     *
     * @author app2641
     **/
    public final function delete (AbstractModel $model)
    {
        try {
        
        } catch (\Exception $e) {
            throw $e;
        }
    }



    public final function fetchById ($id)
    {
        try {
            $sql = 'SELECT * FROM contents
                WHERE contents.id = ?';

            $result = $this->db->state($sql, $id)->fetch();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }



    /**
     * 指定タイトルのレコードを取得する
     *
     * @author app2641
     **/
    public function fetchByTitle ($title)
    {
        try {
            $sql = 'SELECT * FROM contents
                WHERE contents.title = ?';

            $result = $this->db->state($sql, $title)->fetch();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }



    /**
     * 指定URLのレコードを取得する
     *
     * @param string $url
     * @return stdClass
     **/
    public function fetchByUrl ($url)
    {
        try {
            $sql = 'SELECT * FROM contents
                WHERE contents.url = ?';

            $result = $this->db->state($sql, $url)->fetch();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }



    /**
     * タイトルとキャストIDからコンテンツを取得する
     *
     * @param string $title  コンテンツタイトル
     * @param int $cast_id  キャストのid
     * @author app2641
     **/
    public function fetchByTitleWithCastId ($title, $cast_id)
    {
        try {
            $sql = 'SELECT * FROM contents
                WHERE contents.title = ?
                AND contents.cast_id = ?';

            $result = $this->db
                ->state($sql, array($title, $cast_id))->fetch();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }



    /**
     * 指定DMMIDから該当コンテンツを全取得する
     *
     * @param int $cast_id キャストのDMMID
     * @return array
     **/
    public function fetchAllByCastId ($cast_id)
    {
        try {
            $sql = 'SELECT * FROM contents
                WHERE contents.cast_id = ?
                ORDER BY contents.sale_date ASC';

            $results = $this->db->state($sql, $cast_id)->fetchAll();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $results;
    }
}
