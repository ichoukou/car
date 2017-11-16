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
    if (empty($post['description']) or empty($num) or empty($type) or empty($probability))
        exit(json_encode(['status'=>-1, 'result'=>'请填写必填项'], JSON_UNESCAPED_UNICODE));

    $description = $link->escape_string($post['description']);

    $exists = query("SELECT * FROM ".DB_PREFIX."cfg WHERE `type` = '{$type}'");
    if (!empty($exists->row))
        exit(json_encode(['status'=>-1, 'result'=>'类型数字已经被占用'], JSON_UNESCAPED_UNICODE));

    query("INSERT INTO ".DB_PREFIX."cfg SET " .
        " `description` = '{$description}', `num` = '{$num}', `type` = '{$type}',`probability`='{$probability}' ");

    exit(json_encode(['status'=>1, 'result'=>'新增数据成功'], JSON_UNESCAPED_UNICODE));
}

$param = make_filter($_GET);
$url = create_url($param);

include_once "header.html";
include_once "left.html";
include_once "cfg_add.html";
include_once "footer.html";


