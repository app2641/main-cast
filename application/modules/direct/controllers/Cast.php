<?php

use Cast\Container,
    Cast\Factory\ModelFactory;


class Cast
{

    /**
     * キャストリストのデータを取得する
     *
     * @author app2641
     **/
    public function getList ($request)
    {
        $container  = new Container(new ModelFactory);
        $cast_model = $container->get('CastModel');

        $start = $request->start;
        $limit = $request->limit;

        if (isset($request->query) && $request->query !== '') {
            $query = $request->query;
        } else {
            $query = null;
        }


        $results = $cast_model->query->getList($start, $limit, $query);
        $count   = $cast_model->query->getListCount($query);

        return array(
            'results' => $results,
            'count' => $count
        );
    }



    /**
     * 指定idのキャストレコードのis_activeを更新する
     *
     * @author app2641
     **/
    public function updateIsActive ($request)
    {
        $container  = new Container(new ModelFactory);
        $cast_model = $container->get('CastModel');

        $cast_model->fetchById($request->id);
        $cast_model->set('is_active', !$cast_model->get('is_active'));
        $cast_model->update();

        return array('success' => true);
    }



    /**
     * CastIdの重複したキャスト群を取得する
     *
     * @author app2641
     **/
    public function getDuplicateCastList ($request)
    {
        $container  = new Container(new ModelFactory);
        $cast_model = $container->get('CastModel');

        $results = $cast_model->query->fetchAllByCastId($request->cast_id);
        return $results;
    }



    /**
     * キャストデータをフォームにロードする
     *
     * @param int $id  キャストのid
     * @author app2641
     **/
    public function loadCastData ($request)
    {
        $container  = new Container(new ModelFactory);
        $cast_model = $container->get('CastModel');

        $cast_model->fetchById($request->id);

        return array('success' => true, 'data' => $cast_model->getRecord());
    }



    /**
     * キャストデータを新規作成する
     *
     * @param stdClass $values  フォームデータ
     * @author app2641
     **/
    public function createCastData ($request)
    {
        try {
            $container  = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            $values = $request->values;
            $cast = $cast_model->query->fetchByName($values->name);

            if ($cast != false && $values->cast_id == $cast->cast_id) {
                throw new \Exception('既に同じ名前が存在しています！');
            }

            $params = new \stdClass;
            $params->cast_id = $values->cast_id;
            $params->dmm_name = $values->dmm_name;
            $params->name = $values->name;
            $params->furigana = $values->furigana;
            $cast_model->insert($params);

        } catch (\Exception $e) {
            return array('success' => false, 'msg' => $e->getMessage());
        }

        return array('success' => true);
    }



    /**
     * キャストデータを更新する
     *
     * @param stdClass $values  フォームデータ
     * @author app2641
     **/
    public function updateCastData ($request)
    {
        try {
            $container  = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            $values = $request->values;
            $cast_model->fetchById($values->id);

            $cast_model->set('name', $values->name);
            $cast_model->set('furigana', $values->furigana);
            $cast_model->update();

        } catch (\Exception $e) {
            return array('success' => false, 'msg' => $e->getMessage());
        }

        return array('success' => true);
    }



    /**
     * キャストデータの削除
     *
     * @param int $id  キャストid
     * @author app2641
     **/
    public function deleteCastData ($request)
    {
        try {
            $container  = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            $cast_model->fetchById($request->id);
            $cast_model->delete();

        } catch (\Exception $e) {
            return array('success' => false, 'msg' => $e->getMessage());
        }

        return array('success' => true);
    }
}
