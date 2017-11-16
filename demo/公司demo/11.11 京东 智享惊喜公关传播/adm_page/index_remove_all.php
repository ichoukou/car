<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $ids = $_POST['ids'];
    $type = (int)$_POST['type'];
    if (count($ids) <= 0 and $type == 1)
        exit(json_encode(['status'=>-1, 'result'=>'缺少参数'], JSON_UNESCAPED_UNICODE));

    $sql = "UPDATE ".DB_PREFIX."info SET `deleted`= 2 ";

    if ($type == 1) {
        foreach ($ids as $info_id) {
            $id = (int)$info_id;
            if (empty($id))
                continue;

            query($sql . " WHERE `info_id` = '{$info_id}' AND `deleted` != 2 ");
        }
    } else {
        query($sql . " WHERE `deleted` != 2 ");
    }

    exit(json_encode(['status'=>1, 'result'=>'操作成功'], JSON_UNESCAPED_UNICODE));
}



