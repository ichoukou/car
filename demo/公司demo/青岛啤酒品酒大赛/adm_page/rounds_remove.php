<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

$round_id = $_GET['round_id'];
$param = make_filter($_GET);
$url = create_url($param, ['round_id']);

if (empty($round_id)) {
    setcookie('action_return', json_encode(['title'=>'删除数据失败', 'info'=>'缺少标识，删除数据失败'] , JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 365);
    exit(header("location:rounds.php?{$url}"));
}

$info = query("SELECT * FROM ".DB_PREFIX."rounds WHERE `round_id` = '{$round_id}' AND `deleted` = 1 LIMIT 1 ");
if (empty($info->row)) {
    setcookie('action_return', json_encode(['title'=>'删除数据失败', 'info'=>'没有找到匹配的数据，删除数据失败'] , JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 365);
    exit(header("location:rounds.php?{$url}"));
}

query("UPDATE ".DB_PREFIX."rounds SET `deleted` = 2 WHERE `round_id` = '{$round_id}' ");

setcookie('action_return', json_encode(['title'=>'删除数据成功', 'info'=>'删除数据成功'] , JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 365);
exit(header("location:rounds.php?{$url}"));


