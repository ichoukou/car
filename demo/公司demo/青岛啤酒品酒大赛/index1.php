<?php
include_once "common/config.php";
include_once "db/conn.php";
include_once "startup.php";

$openid = $_COOKIE['openid'] ?: '';
$openid = hsc($openid);

/*
 * 决赛入口文件，$type = 2
 */
$type = 2;
$_SESSION['type'] = $type;

if(!empty($openid)){
    $user = query("SELECT * FROM ".DB_PREFIX."user WHERE `openid`='{$link->escape_string($openid)}'");
}

if(empty($user->num_rows)) {
    get_auto_code(APPID, 'http://hd.wechatdpr.com/'.OSS_ROOT.'/auth_callback.php', 'snsapi_base', $type);
}

#是否开启比赛
$winners = query("SELECT * FROM ".DB_PREFIX."winners WHERE `type` = '{$type}'");

$user = query("SELECT * FROM ".DB_PREFIX."default_user WHERE `openid`='{$link->escape_string($openid)}' AND `group_type` = '{$type}' AND `deleted` = 1");
if (!empty($user->row)) {
    $my_rounds = query("SELECT COUNT(*) AS total FROM (SELECT * FROM ".DB_PREFIX."evaluate WHERE `type` = '{$type}' AND `user_id` = '{$user->row['user_id']}' AND `deleted` = 1 GROUP BY round_id) AS g ");
} else {
    $my_rounds = '';
}

$rounds = query("SELECT * FROM ".DB_PREFIX."rounds WHERE `deleted` = 1 AND `type` = '{$type}' ORDER BY sort ASC");
#$my_rounds->row['total'] = 5;
if (!empty($rounds->rows)) {
    #下面定义的s，1是可评分，2是已评分，3是未开启
    foreach ($rounds->rows as $k=>$v) {
        #没有参与过评分
        if (empty($my_rounds->row['total'])) {
            if ($k == 0) {
                $rounds->rows[$k]['s'] = 1;
            } else {
                $rounds->rows[$k]['s'] = 3;
            }
        } else {
            if ($my_rounds->row['total'] >= $k+1) {
                $rounds->rows[$k]['s'] = 2;
            } elseif($my_rounds->row['total']+1 == $k+1) {
                $rounds->rows[$k]['s'] = 1;
            } else {
                $rounds->rows[$k]['s'] = 3;
            }
        }
    }
}

$banner = query("SELECT * FROM ".DB_PREFIX."banner");

$action = 'index1.php';

include "header.html";
include "index1.html";
include "footer.html";




