<?php
include_once "common/config.php";
include_once "db/conn.php";
include_once "startup.php";

$openid = $_COOKIE['openid'] ?: '';
$openid = hsc($openid);

if (empty($openid)) {
    #缺少openid
    exit(json_encode(['status'=>-1,'result'=>'缺少数据'], JSON_UNESCAPED_UNICODE));
} elseif(!empty($openid)) {
    $user = query("SELECT * FROM ".DB_PREFIX."user WHERE `openid`='{$link->escape_string($openid)}'");
    if (empty($user->num_rows)) exit(json_encode(['status'=>-1, 'result'=>'缺少数据'], JSON_UNESCAPED_UNICODE));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);

    if (empty($post['name']) or empty($post['tel']))
        exit(json_encode(['status'=>-1,'result'=>'请填写全部信息']));

    $name  = $link->escape_string($post['name']);
    $tel        = $link->escape_string($post['tel']);

    query("UPDATE ".DB_PREFIX."log SET `name` = '{$name}',`tel` = '{$tel}' WHERE `openid` = '{$openid}' AND `award_type` > 0 ORDER BY id DESC LIMIT 1");

    exit(json_encode(['status'=>1,'result'=>'保存成功'], JSON_UNESCAPED_UNICODE));
}




