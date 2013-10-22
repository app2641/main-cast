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
    public function getDupiicateCastList ($request)
    {
        var_dump($request);
        exit();
    }
}
