<?php
include_once "common/config.php";
include_once "db/conn.php";
include_once "startup.php";

$openid = $_COOKIE['openid'] ?: '';
$openid = hsc($openid);

if (empty($openid)) {
    #缺少openid
    exit(json_encode(['status'=>-1, 'result'=>'缺少openid'], JSON_UNESCAPED_UNICODE));
} elseif (!empty($openid)) {
    $user = query("SELECT * FROM ".DB_PREFIX."user WHERE `openid`='{$openid}'");
    if (empty($user->num_rows))
        exit(json_encode(['status'=>-1, 'result'=>'openid匹配信息错误'], JSON_UNESCAPED_UNICODE));
}

$type = (int)$_SESSION['type'];

if ($type == 1 or $type == 2) {

    $user = query("SELECT * FROM ".DB_PREFIX."default_user WHERE `openid`='{$link->escape_string($openid)}' AND `group_type` = '{$type}' AND `deleted` = 1");

    $round_id = (int)$_GET['round_id'];

    if (!empty($round_id)) {
        $rounds = query("SELECT * FROM ".DB_PREFIX."rounds WHERE `round_id`='{$round_id}' AND deleted = 1");

        #数据不为空
        if (!empty($rounds->rows)) {
            $user = query("SELECT * FROM ".DB_PREFIX."default_user WHERE `openid`='{$link->escape_string($openid)}' AND `group_type` = '{$rounds->row['type']}'");
            $my_rounds = query("SELECT * FROM ".DB_PREFIX."evaluate WHERE `round_id` = '{$round_id}' AND `user_id` = '{$user->row['user_id']}'");

            #此轮数是否已经被评价过
            if (empty($my_rounds->row['total'])) {
                $infos = query("SELECT * FROM ".DB_PREFIX."info WHERE `round_id`='{$round_id}' AND deleted = 1");

                #当前轮数的下一轮，用于提交完数据以后，jquery后台替换下一轮的样式和绑定事件
                $sql = "SELECT round_id FROM ".DB_PREFIX."rounds WHERE `sort` > '{$rounds->row['sort']}' AND `type` = '{$rounds->row['type']}' AND `deleted` = 1 LIMIT 1";
                $next_rounds = query($sql);

                $options = query("SELECT * FROM ".DB_PREFIX."options WHERE deleted = 1 ORDER BY sort DESC");
                if (!empty($options->rows)) {
                    $likes = $hates = [];
                    foreach ($options->rows as $ok=>$ov) {
                        if ($ov['option_type'] == 1) {
                            array_push($likes, $ov);
                        } else {
                            array_push($hates, $ov);
                        }
                    }
                }
            }
        }
    }
}

include "get_info.html";









