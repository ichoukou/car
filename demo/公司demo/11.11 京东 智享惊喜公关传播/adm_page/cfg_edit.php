<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);

    $num = (int)$post['num'];
    $type = (int)$post['type'];
    $probability = (int)$post['probability'];
    if (empty($post['description']) or empty($type))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $description = $link->escape_string($post['description']);

    $exists = query("SELECT * FROM ".DB_PREFIX."cfg WHERE `type` = '{$type}'");
    if (!empty($exists->row) and $exists->row['id'] != $post['id'])
        exit(json_encode(['status'=>-1, 'result'=>'类型数字已经被占用'], JSON_UNESCAPED_UNICODE));

    query("UPDATE ".DB_PREFIX."cfg SET " .
        " `description` = '{$description}', `num` = num + '{$num}', `type` = '{$type}',`probability`='{$probability}' WHERE `id`='{$post['id']}'");

    exit(json_encode(['status'=>1, 'result'=>'编辑数据成功'], JSON_UNESCAPED_UNICODE));
}

$id = (int)$_GET['id'];
$info = query("SELECT * FROM ".DB_PREFIX."cfg WHERE `deleted` = 1 AND `id` = '{$id}' LIMIT 1 ");
$info = $info->row;

$param = make_filter($_GET);
$url = create_url($param, ['id']);

include_once "header.html";
include_once "left.html";
include_once "cfg_edit.html";
include_once "footer.html";


