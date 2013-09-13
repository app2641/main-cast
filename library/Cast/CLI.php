<?php


namespace Cast;

class CLI
{
    private $id;

    private static $instance;


    /**
     * 生成時に一意のidを生成する
     *
     * @author app2641
     **/
    private function __construct ()
    {
        $this->id = md5(date('r') . mt_rand());
    }


    /**
     * クローン時にExceptionを投げる
     *
     * @author app2641
     **/
    private final function __clone ()
    {
        throw new \Exception('Clone is not allowed against '.get_class($this));
    }



    /**
     * インスタンスを取得する
     *
     * @author app2641
     **/
    public static function getInstance ()
    {
        if (is_null(self::$instance)) {
            self::$instance = new CLI();
        }

        return self::$instance;
    }



    /**
     * 引数で渡された値のコマンドクラスを生成して実行する
     *
     * @author app2641
     **/
    public function execute ($argv)
    {
        array_shift($argv);

        // コマンドクラスの有無を確認する
        if (! file_exists(dirname(__FILE__).'/Commands/'.$argv[0].'.php')) {
            throw new \InvalidArgumentException(sprintf('%s command is not found!', $argv[0]));
        }


        $class_name = 'Cast\Commands\\'.$argv[0];

        array_shift($argv);
        $class = new $class_name;
        $class->execute($argv);
    }


    /**
     * 登録されているコマンドのリストを表示する
     *
     * @author app2641
     **/
    public function renderCommandList ()
    {
        echo "\n" . pack('c',0x1B) . "[1m" . '-- CAST commands list --' . pack('c',0x1B) . "[0m" . "\n";

        // コマンドパス内を精査する
        if ($dh = opendir(CMDS)) {
            while ($entry = readdir($dh)) {

                // phpファイルかどうか
                if ($entry != '.' && $entry != '..' && preg_match('/.*\.php/', $entry)) {

                    $count = 30;
                    $class_path = 'Cast\Commands\\' . str_replace('.php', '', $entry);

                    // コマンドクラスからヘルプメッセージを取得する
                    $help_msg = call_user_func(array($class_path, 'help'));


                    // ヘルプメッセージのマージンのために文字数計算をする
                    $class_name = str_replace('.php', '', $entry);
                    $class_name_count = mb_strlen($class_name);
                    $count = $count - $class_name_count;
                    $txt = '';

                    for ($i = 0; $i < $count; $i++) {
                        $txt .= ' ';
                    }

                    
                    // コンソールにメッセージを表示する
                    echo '  ' . pack('c',0x1B) . "[1;33m" . $class_name . ':' .
                        pack('c',0x1B) . "[0m" . $txt . $help_msg .PHP_EOL;
                }
            }

            closedir($dh);
        }

        echo "\n";
    }
}
