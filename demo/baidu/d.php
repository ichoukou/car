<?php
# https://cloud.baidu.com/doc/OCR/OCR-PHP-SDK.html#.CC.97.73.06.FD.A1.D8.DE.4F.1F.5E.CF.E4.1A.E6.B9 api文档
# http://ai.baidu.com/sdk#vis sdk下载
# https://console.bce.baidu.com/ai/#/ai/imagerecognition/app/list 应用列表

require_once 'AipOcr.php';

const APP_ID = '10370055';
const API_KEY = 'iOGc3jS4CPyq64BVss27Uuq3';
const SECRET_KEY = '3ljHcuWmw56h7qIk7kEHKFILXV68BtnH';

$aipOcr = new AipOcr(APP_ID, API_KEY, SECRET_KEY);

#传入图片
#$result = $aipOcr->enhancedGeneral(file_get_contents('enhanced_general.jpg'));
#$result = $aipOcr->enhancedGeneral('http://www.xxxxxx.com/img.jpg');

# 定义参数变量
$option = ['detect_direction' => 'false', 'language_type' => "CHN_ENG"];

# 调用生僻字识别接口
#$result = $aipOcr->basicGeneral(file_get_contents('../t2.jpg'), $option);
#$result = $aipOcr->enhancedGeneral('https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1510826086272&di=d6ef93ce3afec40798d10b7f828efa9f&imgtype=0&src=http%3A%2F%2Ffile.juzimi.com%2Fshouxiepic%2Fjlzemx2.jpg', $option);
$result = $aipOcr->basicGeneral('http://hd.wechatdpr.com/jd/2017/1111/aaa.jpg', $option);

var_dump($result);