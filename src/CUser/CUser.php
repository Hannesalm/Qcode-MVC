<?php

namespace Anax\CUser;


class CUser extends \Anax\Session {

    public function isAuthenticated(){
        $loggedIn = $this->get('loggedIn');
        var_dump($loggedIn);
        die;
        if(isset($loggedIn) && $this->get('loggedIn') == 1){
            return true;
        }
        return false;
    }

    public static function getName(){
        return $_SESSION['userName'];
    }
}