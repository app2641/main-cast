<?php


namespace Cast\Model\Query;

use Cast\Container,
    Cast\Factory\ModelFactory;

use Cast\Model\AbstractModel;

class CastQuery implements QueryInterface
{
    protected $db;

    public $column;


    public function __construct ()
    {
        $this->db = \Zend_Registry::get('db');

        $container = new Container(new ModelFactory);
        $this->column = $container->get('CastColumn');
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

            $sql = 'INSERT INTO cast
                (cast_id, dmm_name, name, furigana, url)
                VALUES (:cast_id, :dmm_name, :name, :furigana, :url)';

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

            $sql = 'UPDATE cast
                SET cast_id = :cast_id,
                dmm_name = :dmm_name,
                name = :name,
                furigana = :furigana,
                url = :url,
                search_index = :search_index,
                is_active = :is_active
                WHERE cast.id = :id';

            $this->db->state($sql, $record);

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
            $sql = 'SELECT * FROM cast
                WHERE cast.id = ?';

            $result = $this->db->state($sql, $id)->fetch();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }



    /**
     * レコードを全取得する
     *
     * @author app2641
     **/
    public function fetchAll ()
    {
        try {
            $sql = 'SELECT * FROM cast';
            $results = $this->db->state($sql)->fetchAll();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $results;
    }



    /**
     * 指定した名前を持つレコードを取得する
     *
     * @param string $name  女優名
     * @author app2641
     **/
    public function fetchByName ($name)
    {
        try {
            $sql = 'SELECT * FROM cast
                WHERE cast.name = ?';

            $result = $this->db->state($sql, $name)->fetch();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }



    /**
     * 指定したDMM名を持つレコードを取得する
     *
     * @param string $dmm_name  DMM上でのキャスト名
     * @author app2641
     **/
    public function fetchByDmmName ($dmm_name)
    {
        try {
            $sql = 'SELECT * FROM cast
                WHERE cast.dmm_name = ?';

            $result = $this->db->state($sql, $dmm_name)->fetch();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }



    /**
     * 指定CastIdを持つレコードを取得する
     *
     * @param int $cast_id  DMM用のキャストID
     * @author suguru
     **/
    public function fetchByCastId ($cast_id)
    {
        try {
            $sql = 'SELECt * FROM cast
                WHERE cast.cast_id = ?';

            $result = $this->db->state($sql, $cast_id)->fetch();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $result;
    }



    /**
     * 検索インデックスを作成していないキャスト群を取得する
     *
     * @author app2641
     **/
    public function notSearchIndexCasts ()
    {
        try {
            $sql = 'SELECT * FROM cast
                WHERE cast.search_index = ?';

            $results = $this->db
                ->state($sql, false)->fetchAll();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $results;
    }




    /**
     * キャストリストデータを取得する
     *
     * @param int $start  ページャスタート値
     * @param int $limit  必要個数
     * @param string $query  頭文字クエリ
     * @author app2641
     **/
    public function getList ($start, $limit, $query = null)
    {
        try {
            $sql = 'SELECT * FROM cast ';
            $bind = array();

            // 頭文字クエリ
            if (! is_null($query)) {
                $sql .= 'WHERE cast.furigana LIKE ? ';
                $bind[] = $query.'%';
            }


            // ORDER BY句の記載
            $sql .= 'ORDER BY cast.furigana ASC ';

            // LINIT句の記載
            $sql .= "LIMIT %s, %s";
            $sql = sprintf($sql, $start, $limit);

            $results = $this->db->state($sql, $bind)->fetchAll();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $results;
    }



    /**
     * キャストリストデータの個数を取得する
     *
     * @param string $query  頭文字クエリ
     * @author app2641
     **/
    public function getListCount ($query = null)
    {
        try {
            $sql = 'SELECT count(cast.id) AS count
                FROM cast ';
            $bind = array();

            if (! is_null($query)) {
                $sql .= 'WHERE cast.furigana LIKE ? ';
                $bind[] = $query.'%';
            }

            $result = $this->db->state($sql, $bind)->fetch();
        
        } catch (\Exception $e) {
            throw $e;
        }

        return $result->count;
    }
}
