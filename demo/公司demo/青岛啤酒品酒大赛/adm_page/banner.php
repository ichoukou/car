<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($_FILES['image_url']['error'] != 0)
        exit(json_encode(['status'=>-1, 'result'=>'图片不能为空'], JSON_UNESCAPED_UNICODE));

    #file_path 是绝对路径 不带文件名称的
    $file_path = '../'.UPDIR.'adm/';
    #file_name 不带后缀的文件名称 就是自己定义一个文件名称
    $file_name = uniqid().'_banner_image';

    $image=moveUploadedImage($_FILES, 'image_url', $file_path, $file_name, 41943040);

    if (!$image)
        exit(json_encode(['status'=>-1, 'result'=>'图片最大40MB，且必须是PNG或JPG'], JSON_UNESCAPED_UNICODE));

    $file_path = UPDIR.'adm/'.$image;

    require '../lib/sdk.class.php';

    $oss_sdk_service = new ALIOSS();
    $bucket = 'wechatdpr';

    $s[] = filesize('../' . $file_path);
    $options = [
        ALIOSS::OSS_FILE_UPLOAD => '../' . $file_path,
        'partSize' => "$s[0]"
    ];

    $object = OSS_ROOT . DS . $file_path;

    $response = $oss_sdk_service->create_mpu_object($bucket, $object, $options);

    $new_image = OSS_URL . $file_path;

    query("UPDATE ".DB_PREFIX."banner SET " .
        " `image_url` = '{$new_image}' ");

    exit(json_encode(['status'=>1, 'result'=>'编辑数据成功'], JSON_UNESCAPED_UNICODE));
}

$info = query("SELECT * FROM ".DB_PREFIX."banner LIMIT 1 ");
$info = $info->row;

include_once "header.html";
include_once "left.html";
include_once "banner.html";
include_once "footer.html";


