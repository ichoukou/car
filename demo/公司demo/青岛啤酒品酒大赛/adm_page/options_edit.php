<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);
    $option_id = $link->escape_string($post['option_id']);

    if (empty($post['title']) or empty($option_id) or empty($post['option_type']))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $title = $link->escape_string($post['title']);
    $option_type  = (int)$post['option_type'];
    $sort = (int)$post['sort'];

    query("UPDATE ".DB_PREFIX."options SET " .
        " `title` = '{$title}', `option_type` = '{$option_type}', `sort` = '{$sort}'  WHERE `option_id` = '{$option_id}' ");

    exit(json_encode(['status'=>1, 'result'=>'编辑数据成功'], JSON_UNESCAPED_UNICODE));
}

$option_id = (int)$_GET['option_id'];
$info = query("SELECT * FROM ".DB_PREFIX."options WHERE `deleted` = 1 AND `option_id` = '{$option_id}' LIMIT 1 ");
$info = $info->row;

$param = make_filter($_GET);
$url = create_url($param, ['option_id']);

include_once "header.html";
include_once "left.html";
include_once "options_edit.html";
include_once "footer.html";


