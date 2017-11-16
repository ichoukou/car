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
    exit(header("location:cfg.php?{$url}"));
}

$info = query("SELECT * FROM ".DB_PREFIX."cfg WHERE `id` = '{$info_id}' LIMIT 1 ");
if (empty($info->row)) {
    setcookie('action_return', json_encode(['title'=>'删除数据失败', 'info'=>'没有找到匹配的数据，删除数据失败'] , JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 365);
    exit(header("location:cfg.php?{$url}"));
}

if ($info->row['deleted'] == 2) {
    $d = 1;
} else {
    $d = 2;
}
query("UPDATE ".DB_PREFIX."cfg SET `deleted` = '{$d}' WHERE `id` = '{$info_id}' ");

setcookie('action_return', json_encode(['title'=>'删除数据成功', 'info'=>'删除数据成功'] , JSON_UNESCAPED_UNICODE), time() + 3600 * 24 * 365);
exit(header("location:cfg.php?{$url}"));


