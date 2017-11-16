<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);
    $round_id = (int)$post['round_id'];
    $sort = (int)$post['sort'];
    $type = (int)$post['type'];

    if (empty($post['title']))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $title = $link->escape_string($post['title']);
    if ($type != 1 and $type != 2)
        $_GET['type'] = $type = 1;

    $exists = query("SELECT COUNT(*) AS total FROM ".DB_PREFIX."rounds WHERE " .
        " `sort` = '{$sort}' AND `type` = '{$type}' AND `round_id` != '{$round_id}' AND `deleted` = 1 ");

    if ($exists->row['total'] > 0)
        exit(json_encode(['status'=>-1, 'result'=>'此排序值已经被使用，请换成其他值'], JSON_UNESCAPED_UNICODE));

    query("UPDATE ".DB_PREFIX."rounds SET " .
        " `title` = '{$title}', `sort` = '{$sort}' WHERE `round_id` = '{$round_id}' ");

    exit(json_encode(['status'=>1, 'result'=>'编辑数据成功'], JSON_UNESCAPED_UNICODE));
}

$round_id = (int)$_GET['round_id'];
$info = query("SELECT * FROM ".DB_PREFIX."rounds WHERE `deleted` = 1 AND `round_id` = '{$round_id}' LIMIT 1 ");
$info = $info->row;

$param = make_filter($_GET);
$url = create_url($param, ['round_id']);
$type = (int)$_GET['type'];

include_once "header.html";
include_once "left.html";
include_once "rounds_edit.html";
include_once "footer.html";


