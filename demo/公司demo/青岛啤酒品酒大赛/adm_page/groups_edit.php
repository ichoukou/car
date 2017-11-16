<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);
    $group_id = (int)$post['group_id'];

    if (empty($post['title']))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $title = $link->escape_string($post['title']);

    query("UPDATE ".DB_PREFIX."groups SET " .
        " `title` = '{$title}' WHERE `group_id` = '{$group_id}' ");

    exit(json_encode(['status'=>1, 'result'=>'编辑数据成功'], JSON_UNESCAPED_UNICODE));
}

$group_id = (int)$_GET['group_id'];
$info = query("SELECT * FROM ".DB_PREFIX."groups WHERE `deleted` = 1 AND `group_id` = '{$group_id}' LIMIT 1 ");
$info = $info->row;

$param = make_filter($_GET);
$url = create_url($param, ['group_id']);

include_once "header.html";
include_once "left.html";
include_once "groups_edit.html";
include_once "footer.html";


