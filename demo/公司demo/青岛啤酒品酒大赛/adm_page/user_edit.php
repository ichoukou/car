<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);
    $user_id = $link->escape_string($post['user_id']);

    if (empty($post['name']) or empty($user_id) or empty($post['channel']))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $name = $link->escape_string($post['name']);
    $channel = $link->escape_string($post['channel']);

    query("UPDATE ".DB_PREFIX."default_user SET " .
        " `name` = '{$name}', `channel` = '{$channel}' WHERE `user_id` = '{$user_id}' ");

    exit(json_encode(['status'=>1, 'result'=>'编辑数据成功'], JSON_UNESCAPED_UNICODE));
}

$user_id = (int)$_GET['user_id'];
$info = query("SELECT * FROM ".DB_PREFIX."default_user WHERE `deleted` = 1 AND `user_id` = '{$user_id}' LIMIT 1 ");
$info = $info->row;

$param = make_filter($_GET);
$url = create_url($param, ['user_id']);

$channels = query("SELECT * FROM ".DB_PREFIX."channels WHERE `deleted` = 1");

include_once "header.html";
include_once "left.html";
include_once "user_edit.html";
include_once "footer.html";


