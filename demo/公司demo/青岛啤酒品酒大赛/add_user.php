<?php
include_once "common/config.php";
include_once "db/conn.php";
include_once "startup.php";

$openid = $_COOKIE['openid'] ?: '';
$openid = hsc($openid);

if (empty($openid)) {
    #缺少openid
    exit(json_encode(['status'=>-1,'result'=>'缺少数据']));
} elseif(!empty($openid)) {
    $user = query("SELECT * FROM ".DB_PREFIX."user WHERE `openid`='{$link->escape_string($openid)}'");
    if (empty($user->num_rows)) exit(json_encode(['status'=>-1, 'result'=>'缺少数据']));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $post = hsc($_POST);

    if (!empty($post['name'])) {
        $name = $link->escape_string($post['name']);

        $default_user = query("SELECT * FROM ".DB_PREFIX."default_user WHERE `name`='{$link->escape_string($name)}' AND `group_type` = '{$post['type']}' AND `deleted` = 1");
        if (empty($default_user->row)) {
            exit(json_encode(array('status'=>-1,'result'=>'姓名匹配错误')));
        } elseif(!empty($default_user->row['openid'])) {
            exit(json_encode(array('status'=>-1,'result'=>'此姓名已经绑定')));
        }

        query("UPDATE ".DB_PREFIX."default_user SET `openid`='{$openid}' WHERE `user_id` = '{$default_user->row['user_id']}'");
        exit(json_encode(array('status'=>1,'result'=>'提交成功')));
    } else {
        exit(json_encode(array('status'=>-1,'result'=>'请填写姓名')));
    }
}




