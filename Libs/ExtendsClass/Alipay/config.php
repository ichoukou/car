<?php
$config = [
	#应用ID,您的APPID。
	'app_id' => '2017111800028220',
	#商户私钥，您的原始格式RSA私钥
	'merchant_private_key' => 'zAJrVygp2Zi0uJiwNIR6zA==',
	#异步通知地址
	'notify_url' => HTTP_SERVER . $this->data['entrance'] . 'route=Front/User/Pay/pay_notify',
	#同步跳转
	'return_url' => HTTP_SERVER . $this->data['entrance'] . 'route=Front/User/Pay/pay_return',
	#编码格式
	'charset' => "UTF-8",
	#签名方式
	'sign_type'=>"RSA2",
	#支付宝网关
	'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
	#支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
	'alipay_public_key' => "",
];