<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);

    if (empty($post['title']) or empty($post['group_type']))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $title = $link->escape_string($post['title']);
    $group_type = $link->escape_string($post['group_type']);

    query("INSERT INTO ".DB_PREFIX."groups SET " .
        " `title` = '{$title}', `group_type` = '{$group_type}' ");

    exit(json_encode(['status'=>1, 'result'=>'新增数据成功'], JSON_UNESCAPED_UNICODE));
}

$param = make_filter($_GET);
$url = create_url($param);

include_once "header.html";
include_once "left.html";
include_once "groups_add.html";
include_once "footer.html";


