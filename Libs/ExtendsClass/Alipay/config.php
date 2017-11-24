<?php
$config = [
	#应用ID,您的APPID。
	'app_id' => '2017111800028220',
	#商户私钥，您的原始格式RSA私钥
	'merchant_private_key' => 'MIICXwIBAAKBgQDHXj2uTjbTjpsHbWcp+qJCM7rReuj9p1mF571RoRsKk3QwVrDRzSC8wdmMNnVYjykNVY2uHNT0Fz02AzzxJs5QYf9HFkVqpGyXO74IAm6VWYQdCKz0Oc8SX3lRH+wOxdYwdWhHrzWkFlhWs2VBVWMzRX0NgihjZA5XV3s3pU+6SwIDAQABAoGBAKc4+s+LNx13zb0LHK7vVM3midpPF8I6bjKM0BT55q6WQ6yP6jvvrHfmuMyMmRyw56QRkIbsFAqwu1zklmdHP2BbtM0CVrNiFRaupsyYBMoLK0hMto/a0PPeEdi9Ncya+JWCHSr9QdL4qbcHQD7oSG+mpHEkzPOgDMamXHFYE4eBAkEA++7vxP61NTJFqw2cRwbMuO5yNxl2MlAztO5JhUQPenTFfjFe6MAkX+ppfaSuaL8OYLlMrdIJsg36CElZtuQ1qwJBAMqWFtlK2tYoEY816kB/m+thbTI053y6s2ps+BdS7CZ2gx+WmRJnJ2vbYP1sa24JZhZ5VYF6XcUvzsArrKNoreECQQDzB8MLAT7jS1TDtwR7zAZvm65YktpfnNjUgE4yHb7kvYLKwmuxEbHFSUdsahHJgA0pp3dd8tFhj3QUzFWcRbezAkEAyGKnnS2+wqCFpeuFl2DiIHSRJ11PMIAYYggBaki6j90gHEnBhYOFadlo2aO5q/EeCtLfsiAQyrBcSJeqv0QW4QJBANXa9XcMGHku54+OnGib9yNqIZIsrhDNs2lxCTDYuKD6jAKssjyJEjeGAPGPm24iFiUfpri8AdY2bdcO1Jk4wEI=',
    #AES秘钥
    #'aes_private_key' => 'zAJrVygp2Zi0uJiwNIR6zA==',
    #异步通知地址
	'notify_url' => HTTP_SERVER . $this->data['entrance'] . 'route=Front/User/Pay/pay_notify',
	#同步跳转
	'return_url' => HTTP_SERVER . $this->data['entrance'] . 'route=Front/User/Pay/pay_return',
	#编码格式
	'charset' => "UTF-8",
	#签名方式
	'sign_type' => 'RSA2',
	#支付宝网关
	'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
	#支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
	'alipay_public_key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB',
];

