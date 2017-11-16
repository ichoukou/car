<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";

$login_error = $registered_success = $_SESSION['admin_id'] = $_SESSION['username'] = $_SESSION['system_group'] = '';

if (!empty($_SESSION['login_error'])) {
    $login_error = $_SESSION['login_error'];
    $_SESSION['login_error'] = '';
}

if (!empty($_SESSION['registered_success'])) {
    $registered_success = $_SESSION['registered_success'];
    $_SESSION['registered_success'] = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    if (mb_strlen($username) < 5 or empty($password))
        exit(json_encode(['status'=>-1, 'result'=>'账号最少5位或密码为空'], JSON_UNESCAPED_UNICODE));

    $sql = "SELECT * FROM " . DB_PREFIX . "admin WHERE `username` = '{$link->escape_string($username)}' AND `password` = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $link->escape_string($password) . "')))))";
    $admin_info = query($sql);

    if (!empty($admin_info->row['admin_id'])) {
        $_SESSION['admin_id'] = $admin_info->row['admin_id'];
        $_SESSION['username'] = $admin_info->row['username'];
        $_SESSION['system_group'] = $admin_info->row['system_group'];

        exit(json_encode(['status'=>1, 'result'=>'登陆成功'], JSON_UNESCAPED_UNICODE));
    } else {
        exit(json_encode(['status'=>-1, 'result'=>'账号或密码错误'], JSON_UNESCAPED_UNICODE));
    }
}

include_once 'login.html';


