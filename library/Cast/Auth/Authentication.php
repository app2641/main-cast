<?php


namespace Cast\Auth;

use Cast\Container,
    Cast\Factory\ModelFactory;

class Authentication
{
    // ログインセッション名
    private $storage_id = 'cast_auth_session';
    

    /**
     * 現在ログインをしているかどうかを精査する
     *
     * @return boolean
     **/
    public function preAuth ()
    {
        // auto_signin用のクッキーを所持しているか
        if (isset($_COOKIE['remember'])) {
            $key = $_COOKIE['remember'];
        
        } else {
            // ログインセッションを保持しているかどうか
            $storage = $this->getStorage();

            if ($storage) {
                return true;
            } else {
                return false;
            }
        }
    }



    /**
     * ログインセッション名を取得する
     *
     * @return string
     **/
    public function getStorageId ()
    {
        return $this->storage_id;
    }



    /**
     * ログインセッションを取得する
     *
     * @return stdClass or boolean
     **/
    public function getStorage ()
    {
        if (isset($_SESSION[$this->getStorageId()])) {
            return $_SESSION[$this->getStorageId()];
        } else {
            return false;
        }
    }



    /**
     * ログインセッションを設定する
     *
     * @author app2641
     **/
    public function setStorage (\stdClass $std)
    {
        if (isset ($std->salt)) {
            unset($std->salt);
        }

        if (isset($std->password)) {
            unset($std->password);
        }

        if (isset($std->auto_signin_key)) {
            unset($std->auto_signin_key);
        }


        $_SESSION[$this->getStorageId()] = $std;
    }



    /**
     * セッション情報を削除する
     *
     * @author app2641
     **/
    public function clearStorage ()
    {
        unset($_SESSION[$this->getStorageId()]);
    }



    /**
     * ユーザ名とパスワードが合っているかどうか
     *
     * @author app2641
     **/
    public function validate ($username, $password)
    {
        $container = new Container(new ModelFactory);
        $user_table = $container->get('UserTable');

        $auth_user = $user_table->fetchByName($username);

        if ($auth_user) {
            $login_pass = sha1($auth_user->salt.$password);

            // パスワードが合致しているかどうか
            if ($login_pass == $auth_user->password) {
                $user = $user_table->fetchById($auth_user->id);
            
            } else {
                return false;
            }
        
        } else {
            return false;
        }

        return $user;
    }
}
