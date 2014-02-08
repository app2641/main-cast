<?php


namespace Cast\Model;

use Cast\Container,
    Cast\Factory\ModelFactory;

class CastModel extends AbstractModel
{
    public $query;


    public function __construct ()
    {
        $container = new Container(new ModelFactory);
        $this->query = $container->get('CastQuery');
    }



    /**
     * キャスト名とふりがなを解析して検索インデックス用の配列にする
     *
     * @return Array
     **/
    public function decompositionName ()
    {
        mb_internal_encoding('UTF-8');
        $data  = array();

        foreach (array($this->get('name'), $this->get('furigana')) as $value) {
            $length = mb_strlen($value);

            // 最低文字列2から文字数分ループ処理を行う 
            for ($i = 2; $i <= $length; $i++) {

                // substr始点から$i分の文字列を切り取る
                for ($r = 0; $r <= ($length - $i); $r++) {
                    $data[] = mb_substr($value, $r, $i, 'UTF-8');
                }
            }
        }

        return $data;
    }
}
