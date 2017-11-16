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
    $num = (int)$_POST['num'];

    $all_times = query("SELECT COUNT(*) AS total FROM ".DB_PREFIX."log WHERE `openid`='{$openid}'");
    if ($all_times->row['total'] >= 7)
        exit(json_encode(['status'=>-2,'result'=>'抽奖机会已用完'], JSON_UNESCAPED_UNICODE));

    if ($num < 1 or $num > 7)
        exit(json_encode(['status'=>-1,'result'=>'数据错误'], JSON_UNESCAPED_UNICODE));

    $option_info = query("SELECT * FROM ".DB_PREFIX."options WHERE `openid`='{$link->escape_string($openid)}' AND `num`='{$num}'");
    if (empty($option_info->row)) {
        query("INSERT INTO ".DB_PREFIX."options SET `openid` = '{$openid}',`num` = '{$num}'");
        exit(json_encode(['status'=>1,'result'=>'保存成功'], JSON_UNESCAPED_UNICODE));
    } else {
        $award_info = query("SELECT * FROM ".DB_PREFIX."log WHERE `openid`='{$link->escape_string($openid)}' AND `option_num`='{$num}'");
        if (!empty($award_info->row))
            exit(json_encode(['status'=>-1,'result'=>'已经抽过奖'], JSON_UNESCAPED_UNICODE));

        exit(json_encode(['status'=>1,'result'=>'可以抽奖'], JSON_UNESCAPED_UNICODE));
    }
}




