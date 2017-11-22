<?php
require_once 'send_code.php';

$target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
$post['tel'] = 13333333333;
$send_message = "您已预约成功，请携带介绍信按时到馆参观。如有变动请于9：00-16：30拨打0532-68868777告知。";

$post_data = "account=C86661249&password=9d8c28f1f943d3181abe564930e1201b&mobile=".$post['tel']."&content=".rawurlencode("{$send_message}");
$gets = xml_to_array(Post($post_data, $target));

if ($gets['SubmitResult']['code']==2) {
    $send_message_return = '短信发送成功，' . $gets['SubmitResult']['msg'];
} else {
    $send_message_return = '短信发送失败，' . $gets['SubmitResult']['msg'];
}

var_Dump($send_message_return);
