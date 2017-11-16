<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

$info_id = $_GET['info_id'];
$param = make_filter($_GET);
$url = create_url($param, ['info_id']);

if (empty($info_id)) {
    setcookie('action_return', json_encode(['title'=>'删除数据失败', 'info'=>'缺少标识，删除数据失败'] , JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 365);
    exit(header("location:index.php?{$url}"));
}

$info = query("SELECT * FROM ".DB_PREFIX."info1 WHERE `info_id` = '{$info_id}' AND `deleted` = 1 LIMIT 1 ");
if (empty($info->row)) {
    setcookie('action_return', json_encode(['title'=>'删除数据失败', 'info'=>'没有找到匹配的数据，删除数据失败'] , JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 365);
    exit(header("location:index.php?{$url}"));
}

query("UPDATE ".DB_PREFIX."info1 SET `deleted` = 2 WHERE `info_id` = '{$info_id}' ");

setcookie('action_return', json_encode(['title'=>'删除数据成功', 'info'=>'删除数据成功'] , JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 365);
exit(header("location:index.php?{$url}"));


