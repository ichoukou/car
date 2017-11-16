<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

$status = (int)$_GET['status'];

$status = empty($status) ? 2 : $status;

query("UPDATE ".DB_PREFIX."winners SET `status` = {$status} WHERE `type` = 2");

$param = make_filter($_GET);
$url = create_url($param, ['status']);

setcookie('action_return', json_encode(['title'=>'赛事', 'info'=>'赛事状态修改成功'] , JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 365);
exit(header("location:rounds.php?{$url}"));


