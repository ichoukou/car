<?php
// +----------------------------------------------------------------------
// | Hero v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.anlulu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: couth <coolboy@outlook.com> <http://www.anlulu.com>
// +----------------------------------------------------------------------
require_once '../sdk.class.php';

$oss_sdk_service = new ALIOSS();
//$file = $_FILES['upfile'];
$bucket = 'huodong2015';
$object = 'lsh-'.time().'.jpg';  //英文

var_dump($_POST['upfile']);

$options =  isset($_POST['upfile'])?$_POST['upfile']:'C:\Users\Administrator\Desktop\bg.jpg';
		//'partSize' => $file['size'],


$response = $oss_sdk_service->upload_file_by_file($bucket, $object,$options);

//$r = $obj->upload_file_by_content($bucket, $object,$file['tmp_name']);
var_dump($response);

	echo 'OK';
/* End of file */