<?php


namespace Cast\Commands;

class deploy extends Base\AbstractCommand
{

    /**
     * コマンドの実行
     *
     **/
    public function execute (Array $params)
    {
        try {
            if (! isset($params[0])) {
                throw new \Exception ('接続先[argv1]を指定してください');
            }
            

            $ignore = APPLICATION_PATH.'/configs/ignore.txt';

            if (! file_exists($ignore)) {
                throw new \Exception ('ignore.txtが存在しません');
            }



            $ini = APPLICATION_PATH.'/configs/deploy.ini';

            if (! file_exists($ini)) {
                throw new \Exception ('deploy.iniが存在しません');
            }



            $config = new \Zend_Config_Ini($ini);

            if (! isset($config->{$params[0]})) {
                throw new \Exception (sprintf('iniファイルに接続先%sはありません', $params[0]));
            }

            $config = $config->{$params[0]};

            if (! isset($config->host)) {
                throw new \Exception ('hostが設定されていません');
            }

            if (! isset($config->dir)) {
                throw new \Exception ('dirが設定されていません');
            }

            $host = $config->host;
            $dir = $config->dir;
            $user = isset($config->user) ? $config->user . '@' : '';

            if (substr($dir, -1) != '/') {
                $dir .= '/';
            }

            $ssh = 'ssh';

            if (isset($config->port)) {
                if (isset($config->key)) {

                    $instance_dir = dirname(__FILE__);
                    $instance_dir = explode(DS, $instance_dir);

                    foreach ($instance_dir as $k => $d) {
                        if ($d == 'Users') {
                            $key = $k;
                            break;
                        }
                    }

                    $user_dir = $instance_dir[$key + 1];
                    $key = sprintf($config->key, $user_dir);

                } else {
                    throw new \Exception('key設定が間違っています');
                }

                $port = $config->port;
                $ssh = '"ssh -p' . $port . ' ' . $key . '"';
            }

            if (isset($config->parameters)) {
                $parameters = $config->parameters;
            } else {
                throw new \Exception('parameter設定が不明です');
            }

            if (isset($params[1])) {
                if ($params[1] === 'go') {
                    $dry = '';
                } else {
                    $dry = '--dry-run';
                }
            } else {
                $dry = '--dry-run';
            }

            $root = ROOT_PATH.DS;

            $command = "rsync $dry $parameters$ignore -e $ssh $root $user$host:$dir";

            $descriptorspec = array(
                1 => array('pipe', 'w'), // stdout
                2 => array('pipe', 'w'), // stderr
            );

            $process = proc_open($command, $descriptorspec, $pipes);

            if(! is_resource($process)) {
                throw new \Exception('コマンドが実行できません');
            }

            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);

            $output = '';
            $err = '';
            $buffer = '';

            while (!feof($pipes[1]) || !feof($pipes[2])) {
                foreach ($pipes as $k => $pipe) {
                    if (! $line = fread($pipe, 128)) {
                        continue;
                    }

                    if ($k == 1) {
                        $output .= $line;

                        if (false !== $pos = strpos($line, "\n")) {
                            $buffer .= substr($line, 0, $pos);
                            echo "$buffer \n";
                            $buffer = substr($line, $pos + 1);
                        } else {
                            $buffer .= $line;
                        }
                    } else {
                        $err .= $line;

                        if (false !== $pos = strpos($line, "\n")) {
                            $buffer .= substr($line, 0, $pos);
                            echo "error: $buffer \n";
                            $buffer = substr($line, $pos + 1);
                        } else {
                            $buffer .= $line;
                        }
                    }
                }
                usleep(100000);
            }
        
        } catch (\Exception $e) {
            $this->errorLog($e->getMessage());
            return false;
        }
    }



    /**
     * コマンドリストに表示するヘルプメッセージを表示する
     *
     **/
    public static function help ()
    {
        /* write help message */
        $msg = 'デプロイコマンド。設定ファイルはapplication/config/deploy.ini';

        return $msg;
    }
}
