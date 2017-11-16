<?php
include_once "common/config.php";
include_once "db/conn.php";
include_once "startup.php";

$openid = $_COOKIE['openid'] ?: '';
$openid = hsc($openid);

if (empty($openid)) {
    #缺少openid
    exit(json_encode(['status'=>-1,'result'=>'缺少openid'], JSON_UNESCAPED_UNICODE));
} elseif (!empty($openid)) {
    $user = query("SELECT * FROM ".DB_PREFIX."user WHERE `openid`='{$openid}'");
    if (empty($user->num_rows))
        exit(json_encode(['status'=>-1, 'result'=>'openid匹配信息错误'], JSON_UNESCAPED_UNICODE));
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    #$t = date("YmdH");
    #if((int)$t < 2016020719) return false;

    #今天日期
    $date = date("Ymd");
    #$description存在数据库的未中奖或已中奖描述,$show前端显示文案
    $show = $description = "人生最重要的两个字就是坚持，继续玩吧~";
    #红包发送返回默认值
    $return = [];
    #返回给前端的中奖类型值,默认为0（未中奖）
    $type = false;
    #随机到的默认概率
    $rand = 0;
    #抽奖概率
    $guess_num = 1000;
    #剩余抽奖次数
    $nums = 0;

    #获得设置的抽奖概率
    $guess = query("SELECT num FROM ".DB_PREFIX."guess");
    $guess_num = $guess->row['num'];

    #统计当天抽奖是否超过3次
    $my_today_count = query("SELECT COUNT(*) AS total FROM ".DB_PREFIX."log WHERE `date`='{$date}' AND `openid`='{$openid}'");

    #今天的总抽奖次数大于3
    if($my_today_count->row['total'] >= 3)
        exit(json_encode(array('status'=>-1, 'result'=>'人生最重要的两个字就是坚持，继续玩吧~', 'type'=>$type, 'nums'=>$nums)));

    #计算每天剩余抽奖次数 每天最多3次
    $nums = (int)3-(int)$my_today_count->row['total'];

    #每天最多抽出两个奖品
    $today_count = query("SELECT COUNT(*) AS total FROM ".DB_PREFIX."log WHERE `date`='{$date}' AND `award_type` != 0");
//    if($today_count->row['total'] >= 2)
//        exit(json_encode(array('status'=>-1, 'result'=>'人生最重要的两个字就是坚持，继续玩吧~', 'type'=>$type, 'nums'=>$nums)));

    #如果中过奖,再抽奖为未中奖
    $my_winning = query("SELECT COUNT(*) AS total FROM ".DB_PREFIX."log WHERE `openid`='{$openid}' AND `award_type` != 0");

    #获得抽奖总概率
    $probability = query("SELECT SUM(probability) AS all_probability FROM ".DB_PREFIX."cfg WHERE `num` > 0");

    #没中过奖 并且 抽奖次数大于0 并且 还有奖品 才可以抽奖
    if (empty($my_winning->row['total']) and $nums > 0 and $probability->row['all_probability'] > 0 and $today_count->row['total'] < 2) {
        #获得所有可抽奖的奖品信息
        $cfgs = query("SELECT * FROM ".DB_PREFIX."cfg WHERE `num` > 0 ORDER BY type DESC");

        $rand = mt_rand(1, (int)$probability->row['all_probability'] + (int)$guess_num);#概率

        $now_probability = 0;
        foreach ($cfgs->rows as $k=>$v) {
            if ($rand > $now_probability and $rand <= $now_probability + (int)$v['probability']) {#判断概率
                if ($v['num'] > 0) {
                    #减数量
                    query("UPDATE ".DB_PREFIX."cfg SET `num`=`num`-1 WHERE `type` = '{$v['type']}' AND `num`> 0");

                    $description = $v['description'];
                    $type = $v['type'];

                    break;
                }
            } else {
                $now_probability += (int)$v['probability'];
            }
        }
    }

    $info = [
        'description' => $description,
        'award_type' => $type,
        'rand_num'=>$rand
    ];

    $sql = '';
    foreach ($info as $k=>$v) {
        $sql .= ",`{$k}`='{$v}'";
    }

    $realip = getIp();

    query("INSERT INTO ".DB_PREFIX."log SET `date`='{$date}',`realip`='{$realip}',`openid`='{$openid}' {$sql}");

    exit(json_encode(['status'=>1, 'result'=>$description, 'type'=>$type, 'nums'=>$nums], JSON_UNESCAPED_UNICODE));
}


