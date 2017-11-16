<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);

    if (empty($post['title']) or empty($post['round_id']))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $title = $link->escape_string($post['title']);
    $round_id = $link->escape_string($post['round_id']);

    $exists = query("SELECT * FROM ".DB_PREFIX."rounds WHERE `round_id` = '{$round_id}' AND `deleted` = 1 ");
    if (empty($exists->row))
        exit(json_encode(['status'=>-1, 'result'=>'数据匹配错误'], JSON_UNESCAPED_UNICODE));

    query("INSERT INTO ".DB_PREFIX."info SET " .
        " `title` = '{$title}', `round_id` = '{$round_id}', `type` = '{$exists->row['type']}' ");

    exit(json_encode(['status'=>1, 'result'=>'新增数据成功'], JSON_UNESCAPED_UNICODE));
}

$param = make_filter($_GET);
$url = create_url($param);
$round_id = (int)$_GET['round_id'];

include_once "header.html";
include_once "left.html";
include_once "index_add.html";
include_once "footer.html";


