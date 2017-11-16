<?php
// +----------------------------------------------------------------------
// | Hero v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.anlulu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: couth <coolboy@outlook.com> <http://www.anlulu.com>
// +----------------------------------------------------------------------
require_once 'sdk.class.php';

$oss_sdk_service = new ALIOSS();
//$file = $_FILES['upfile'];
$bucket = 'huodong2015';
$object = $pic;  //英文

$options = array(
		ALIOSS::OSS_FILE_UPLOAD => $_FILES['upfile']['tmp_name'],
		'partSize' => $_FILES['upfile']['size'],
);

$response = $oss_sdk_service->create_mpu_object($bucket, $object,$options);

$pic = 'http://huodong2015.oss-cn-qingdao.aliyuncs.com/'.$pic;
setcookie("picurl",$pic);
/* End of file */