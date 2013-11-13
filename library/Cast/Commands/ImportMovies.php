<?php


namespace Cast\Commands;

use Cast\Container,
    Cast\Factory\ModelFactory;

use Cast\Parser\Video;

require_once ROOT_PATH.'/library/SimpleHtmlDomParser/simple_html_dom.php';

class ImportMovies extends Base\AbstractCommand
{
    protected
        $container;


    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            set_time_limit(0);

            $this->initDatabaseConnection();
            $db = \Zend_Registry::get('db');
            $db->beginTransaction();

            if (! isset($params[0])) {
                throw new \Exception('頭文字を指定してください');
            }

            $this->container = $container  = new Container(new ModelFactory);
            $cast_model = $container->get('CastModel');

            // 指定頭文字のキャストを取得する
            $casts = $cast_model->query->fetchAllByInitial($params[0]);
            foreach ($casts as $cast) {
                // リスト1ページ目の動画情報を取得する
                $pager = $this->_parseMainCastMovies($cast->cast_id);

                // ページャの取得
                $active_pager = array();
                foreach ($pager as $page) {
                    if (preg_match('/^[0-9]*$/', $page->plaintext) &&
                        !in_array($page->plaintext, $active_pager)) {
                        $this->_parseMainCastMovies($cast->cast_id, $page->plaintext);
                        $active_pager[] = $page->plaintext;
                    }
                }
                unset($pager);
            }

            $db->commit();
        
        } catch (\Exception $e) {
            $db->rollBack();
            $this->errorLog($e->getMessage());
        }
    }



    /**
     * 動画一覧ページから単独主演動画だけを抽出して解析する
     *
     * @param int $cast_id  DMM上のキャストID
     * @param int $page  何ページ目のリストを取得するか
     * @author app2641
     **/
    private function _parseMainCastMovies ($cast_id, $page = 1)
    {
        $url = 'http://actress.dmm.co.jp/-/detail/=/actress_id=%s/search=one/page=%s/';
        $url = sprintf($url, $cast_id, $page);

        $list = file_get_html($url);
        if ($list === false) {
            echo $url.PHP_EOL;
            throw new \Exception('リストページを取得出来ませんでした');
        }

        // 動画情報を取得する
        $contents_model = $this->container->get('ContentsModel');

        $movies = $list->find('table#w tbody tr td#mu table tbody tr td.info_works2 a');
        foreach ($movies as $movie) {
            $movie_url = $movie->getAttribute('href');
            if (! preg_match('/digital\/videoa\/-\/detail\/=\/cid=/', $movie_url)) {
                continue;
            }

            $content = $contents_model->query->fetchByUrl($movie_url);
            if (! $content) {
                $video = new Video();
                $video->setUrl($movie_url);
                $video->execute();

                unset($video);
            }

        }

        $pager = $list->find('table#w tbody tr td#mu table tbody tr td[align="right"] a');
        //$list->clear();

        return $pager;
    }



    /**
     * コマンドリストに表示するヘルプメッセージを表示する
     *
     **/
    public static function help ()
    {
        /* write help message */
        $msg = '動画情報をDMMから取得する';

        return $msg;
    }
}
