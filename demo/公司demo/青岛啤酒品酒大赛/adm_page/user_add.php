<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);

    if (empty($post['name']) or empty($post['group_id']) or empty($post['channel']))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $name = $link->escape_string($post['name']);
    $group_id = $link->escape_string($post['group_id']);
    $channel = $link->escape_string($post['channel']);

    $exists = query("SELECT * FROM ".DB_PREFIX."groups WHERE `group_id` = '{$group_id}' AND `deleted` = 1 ");
    if (empty($exists->row))
        exit(json_encode(['status'=>-1, 'result'=>'数据匹配错误'], JSON_UNESCAPED_UNICODE));

    query("INSERT INTO ".DB_PREFIX."default_user SET " .
        " `name` = '{$name}', `channel` = '{$channel}', `group_id` = '{$group_id}', `group_type` = '{$exists->row['group_type']}' ");

    exit(json_encode(['status'=>1, 'result'=>'新增数据成功'], JSON_UNESCAPED_UNICODE));
}

$param = make_filter($_GET);
$url = create_url($param);
$group_id = (int)$_GET['group_id'];

if (empty($group_id))
    exit(header('location:groups.php'));

$channels = query("SELECT * FROM ".DB_PREFIX."channels WHERE `deleted` = 1");

include_once "header.html";
include_once "left.html";
include_once "user_add.html";
include_once "footer.html";


