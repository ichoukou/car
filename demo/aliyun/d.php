<?php
# https://help.aliyun.com/document_detail/53419.html?spm=5176.doc28427.6.562.IbTmdu api文档
# https://help.aliyun.com/knowledge_detail/50180.html SDK下载

error_reporting(E_ALL ^ E_NOTICE);

include_once 'aliyun-php-sdk-core/Config.php';
use Green\Request\V20170112 as Green;
date_default_timezone_set("PRC");

$accessKeyId = 'LTAI5aHXmQ3iIkSS';
$accessKeySecret = 'pKtXcjIfPwOH4mS9d0Aqu6QQMwzuM3';

$iClientProfile = DefaultProfile::getProfile("cn-shanghai", $accessKeyId, $accessKeySecret);
DefaultProfile::addEndpoint("cn-shanghai", "cn-shanghai", "Green", "green.cn-shanghai.aliyuncs.com");
$client = new DefaultAcsClient($iClientProfile);

$request = new Green\ImageSyncScanRequest();
$request->setMethod("POST");
$request->setAcceptFormat("JSON");

$task1 = array(
    'dataId' => uniqid(),
    #'url' => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1510826086272&di=d6ef93ce3afec40798d10b7f828efa9f&imgtype=0&src=http%3A%2F%2Ffile.juzimi.com%2Fshouxiepic%2Fjlzemx2.jpg',
    'url' => 'http://hd.wechatdpr.com/jd/2017/1111/aaa.jpg',
    'time' => round(microtime(true)*1000)
);
$request->setContent(json_encode(array("tasks" => array($task1), "scenes" => ["ocr"])));

try {
    $response = $client->getAcsResponse($request);
    print_r($response);
    if(200 == $response->code){
        $taskResults = $response->data;
        foreach ($taskResults as $taskResult) {
            if(200 == $taskResult->code){
                $sceneResults = $taskResult->results;
                foreach ($sceneResults as $sceneResult) {
                    $scene = $sceneResult->scene;
                    $suggestion = $sceneResult->suggestion;
                    //根据scene和suggetion做相关的处理
                    //do something
                    print_r($scene);
                    print_r($suggestion);
                }
            }else{
                print_r("task process fail:" + $response->code);
            }
        }
    }else{
        print_r("detect not success. code:" + $response->code);
    }
} catch (Exception $e) {
    print_r($e);
}



