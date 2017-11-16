<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/9
 * Time: 10:34
 */

function init() {
    if (!session_id()) {
        ini_set('session.use_only_cookies', 'On');
        ini_set('session.use_trans_sid', 'Off');
        ini_set('session.cookie_httponly', 'On');

        session_set_cookie_params(0, '/');
        session_start();
    }

}

function get_session($data){
    init();
    return $_SESSION[$data];
}

function set_session($name,$data){
    init();
    $_SESSION[$name] = $data;
}