<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);
    $channel_id = $link->escape_string($post['channel_id']);

    if (empty($post['title']) or empty($channel_id) or empty($post['num']))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $title = $link->escape_string($post['title']);
    $num = (int)$post['num'];

    query("UPDATE ".DB_PREFIX."channels SET " .
        " `title` = '{$title}', `num` = '{$num}'  WHERE `channel_id` = '{$channel_id}' ");

    exit(json_encode(['status'=>1, 'result'=>'编辑数据成功'], JSON_UNESCAPED_UNICODE));
}

$channel_id = (int)$_GET['channel_id'];
$info = query("SELECT * FROM ".DB_PREFIX."channels WHERE `deleted` = 1 AND `channel_id` = '{$channel_id}' LIMIT 1 ");
$info = $info->row;

$param = make_filter($_GET);
$url = create_url($param, ['channel_id']);

include_once "header.html";
include_once "left.html";
include_once "channels_edit.html";
include_once "footer.html";


