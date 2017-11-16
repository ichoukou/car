<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    exit;
    $username = htmlspecialchars($_POST['username']);
    $sha1_password = htmlspecialchars($_POST['sha1_password']);
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);

    if (mb_strlen($username) < 5)
        exit(json_encode(['status'=>-1, 'result'=>'账号最少5位'], JSON_UNESCAPED_UNICODE));

    if (mb_strlen($password) < 6)
        exit(json_encode(['status'=>-1, 'result'=>'密码最少6位'], JSON_UNESCAPED_UNICODE));

    if ($password != $confirm_password)
        exit(json_encode(['status'=>-1, 'result'=>'两次输入的密码不同'], JSON_UNESCAPED_UNICODE));

    $sql = "SELECT * FROM " . DB_PREFIX . "admin WHERE `username` = '{$link->escape_string($username)}'";
    $admin_info = query($sql);

    if (empty($admin_info->row['admin_id'])) {
        #密码混淆字符串
        $group = $admin_group[0]['group_id'];
        $salt = registered_token(10);
        $save_password = sha1($salt . sha1($salt . sha1($sha1_password)));

        $sql = "INSERT INTO " . DB_PREFIX . "admin (`group`,`username`,`password`,`tel`,`email`,`salt`) VALUES ";
        $sql .= " ('{$group}','{$link->escape_string($username)}','{$save_password}','','','{$salt}') ";
        query($sql);

        $_SESSION['registered_success'] = '注册成功';

        exit(json_encode(['status'=>1, 'result'=>'注册成功'], JSON_UNESCAPED_UNICODE));
    } else {
        exit(json_encode(['status'=>-1, 'result'=>'注册失败，此管理员账号已经被使用'], JSON_UNESCAPED_UNICODE));
    }
}

include_once 'registered.html';


