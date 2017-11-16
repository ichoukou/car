<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $num = (int)$_POST['num'];

    query("UPDATE ".DB_PREFIX."guess SET `num` = '{$num}'");

    exit(json_encode(['status'=>1, 'result'=>'修改成功'], JSON_UNESCAPED_UNICODE));
} else {
    exit(json_encode(['status'=>-1, 'result'=>'修改失败'], JSON_UNESCAPED_UNICODE));
}
