<?php


namespace Cast\Model\Query;

use Cast\Model\AbstractModel;

interface QueryInterface
{
    // レコードの追加
    public function insert (\stdClass $params);


    // レコードの上書き更新
    public function update (AbstractModel $model);


    // レコードの削除
    public function delete (AbstractModel $model);


    // idを起点にレコードを取得する
    public function fetchById ($id);
}
