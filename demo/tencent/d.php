<?php
# https://github.com/tencentyun/image-php-sdk-v2.0 sdk下载
# https://cloud.tencent.com/document/product/460/8601 api文档
# https://console.cloud.tencent.com/capi 秘钥
require_once 'image-php-sdk-v2.0-master/index.php';
use QcloudImage\CIClient;

$APP_ID = '1251456934';
$API_KEY = 'AKIDmSzKUUgzVL4czFrI3f7NTO8j8LNENMhr';
$SECRET_KEY = '73pTyPiromdKp9HEvbCew2wlV4UimgPw';
$BUCKET_NAME = 'number';

$client = new CIClient($APP_ID, $API_KEY, $SECRET_KEY, $BUCKET_NAME);
$client->setTimeout(30);

var_dump($client->tagDetect(['buffer'=>file_get_contents('../t1.png')]));