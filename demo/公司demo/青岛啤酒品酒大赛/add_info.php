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

    if (!empty($post['infos'])) {
        $sql = "INSERT INTO ".DB_PREFIX."evaluate (`user_id`,`info_id`,`round_id`,`type`,`score`,`likes`,`hates`,`content`,`realip`) VALUES ";

        $s = '';
        $i = 1;
        foreach ($post['infos'] as $info_id=>$sub) {
            $get_info = query("SELECT * FROM ".DB_PREFIX."info WHERE `info_id` = '{$info_id}' AND `deleted` = 1");
            if (empty($get_info->row)) {
                exit(json_encode(array('status'=>-1,'result'=>'样品数据匹配错误或者样品已删除')));
                break;
            }

            $user = query("SELECT * FROM ".DB_PREFIX."default_user WHERE `openid`='{$link->escape_string($openid)}' AND `group_type` = '{$get_info->row['type']}' AND `deleted` = 1");
            if (empty($user->row)) {
                exit(json_encode(array('status'=>-1,'result'=>'用户数据匹配错误或者用户已删除')));
                break;
            }

            if (!empty($s))
                $s .= ',';

            $score = (int)$sub['score'];
            $likes = $link->escape_string($sub['likes']);
            $hates = $link->escape_string($sub['hates']);
            $content = $link->escape_string($sub['content']);
            $realip = getIp();

            if (empty($score)) {
                exit(json_encode(array('status'=>-1,'result'=>'每个样品的综合打分不能为空')));
                break;
            }

//            if ($i <= 3 and empty($score)) {
//                exit(json_encode(array('status'=>-1,'result'=>'前3个样品，每个样品的综合打分不能为空')));
//                break;
//            }

//            if ($i <= 3 and empty($likes) and empty($hates) and empty($content)) {
//                exit(json_encode(array('status'=>-1,'result'=>'前3个样品，每个样品非综合打分项至少选择一项')));
//                break;
//            }

            $s .= "('{$user->row['user_id']}', '{$info_id}', '{$get_info->row['round_id']}', '{$get_info->row['type']}', '{$score}', '{$likes}', '{$hates}', '{$content}', '{$realip}')";

            $i++;
        }

        query($sql . $s);

        exit(json_encode(array('status'=>1,'result'=>'提交成功')));
    } else {
        exit(json_encode(array('status'=>-1,'result'=>'请选择选项')));
    }
}




