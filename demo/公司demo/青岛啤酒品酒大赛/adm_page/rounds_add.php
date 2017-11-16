<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);
    $sort = (int)$post['sort'];
    $type = (int)$post['type'];

    if (empty($post['title']) or empty($sort))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $title = $link->escape_string($post['title']);
    if ($type != 1 and $type != 2)
        $_GET['type'] = $type = 1;

    $exists = query("SELECT COUNT(*) AS total FROM ".DB_PREFIX."rounds WHERE " .
        " `sort` = '{$sort}' AND `type` = '{$type}' AND `deleted` = 1 ");

    if ($exists->row['total'] > 0)
        exit(json_encode(['status'=>-1, 'result'=>'此排序值已经被使用，请换成其他值'], JSON_UNESCAPED_UNICODE));

    query("INSERT INTO ".DB_PREFIX."rounds SET " .
        " `title` = '{$title}', `sort` = '{$sort}', `type` = '{$type}' ");

    exit(json_encode(['status'=>1, 'result'=>'新增数据成功'], JSON_UNESCAPED_UNICODE));
}

$param = make_filter($_GET);
$url = create_url($param);
$type = (int)$_GET['type'];

$max_sort = query("SELECT MAX(sort) as max_sort FROM ".DB_PREFIX."rounds WHERE `type` = '{$type}' AND `deleted` = 1 ");

include_once "header.html";
include_once "left.html";
include_once "rounds_add.html";
include_once "footer.html";


