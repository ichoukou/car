<?php
include_once "common/config.php";
include_once "db/conn.php";
include_once "startup.php";

$openid = $_COOKIE['openid'] ?: '';
$openid = hsc($openid);

if (!empty($openid)){
    $user = query("SELECT * FROM ".DB_PREFIX."user WHERE `openid`='{$link->escape_string($openid)}'");
}

if (empty($user->num_rows)) {
    get_auto_code(APPID, 'http://hd.wechatdpr.com/'.OSS_ROOT.'/auth_callback.php', 'snsapi_userinfo');
}

$options_nums = query("SELECT * FROM ".DB_PREFIX."log WHERE `deleted` = 1 AND `openid` = '{$openid}' GROUP BY option_num");
$all_nums = count($options_nums->rows);
$all_nums_list = '';
if (!empty($options_nums->rows)) {
    foreach ($options_nums->rows as $v) {
        if (!empty($all_nums_list)) $all_nums_list .= ',';

        $all_nums_list .= $v['option_num'];
    }
}

include "header.html";
include "index1.html";
include "footer.html";




