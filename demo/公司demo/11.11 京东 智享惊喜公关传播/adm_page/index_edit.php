<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);
    $info_id = $link->escape_string($post['info_id']);

    if (empty($post['status']))
        exit(json_encode(['status'=>-1, 'result'=>'请选择审核状态'], JSON_UNESCAPED_UNICODE));

    $status = $link->escape_string($post['status']);
    $winner = (int)$post['winner'] == 1 ? 1 : 2;

    query("UPDATE ".DB_PREFIX."info SET " .
        " `status` = '{$status}',`winner` = '{$winner}' WHERE `info_id` = '{$info_id}' ");

    exit(json_encode(['status'=>1, 'result'=>'编辑数据成功'], JSON_UNESCAPED_UNICODE));
}

$info_id = (int)$_GET['info_id'];
$info = query("SELECT * FROM ".DB_PREFIX."info WHERE `deleted` = 1 AND `info_id` = '{$info_id}' LIMIT 1 ");
$info = $info->row;

$param = make_filter($_GET);
$url = create_url($param, ['info_id']);

include_once "header.html";
include_once "left.html";
include_once "index_edit.html";
include_once "footer.html";


