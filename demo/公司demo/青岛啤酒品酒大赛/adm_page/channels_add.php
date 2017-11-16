<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);

    if (empty($post['title']) or empty($post['num']))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $title = $link->escape_string($post['title']);
    $num  = (int)$post['num'];

    query("INSERT INTO ".DB_PREFIX."channels SET " .
        " `title` = '{$title}', `num` = '{$num}' ");

    exit(json_encode(['status'=>1, 'result'=>'新增数据成功'], JSON_UNESCAPED_UNICODE));
}

$param = make_filter($_GET);
$url = create_url($param);

include_once "header.html";
include_once "left.html";
include_once "channels_add.html";
include_once "footer.html";


