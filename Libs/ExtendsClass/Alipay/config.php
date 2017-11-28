<?php
$config = [
	#应用ID,您的APPID。
	'app_id' => '2017111800028220',
	#商户私钥，您的原始格式RSA私钥
	'merchant_private_key' => 'MIIEowIBAAKCAQEA4aRw/bAYwKnRhTGEelHg6gaisU5/xJZ5jQnnjPQqzUQSApxxamB8eIU65Nyv1afnE02u+GZqcbnb074Sr+LlJzhHK3YmyINdHebMNmMRleDJvvVhQFFYKsei2Qd+IlTy8SToNVD3upRI2x7bPj9Tly396upb7QayHnVwp7urBX4akPFLnG8RaMIWYBshbwc6etLi/Arh3fY18AEVe+pzK+SKAWLzO5gKOzlvmwAE9CcUeSrbMEr9qMreWAJO0WrsH2BTc6hG7UcflZST5hi7bK0eY0hc+je9mL6ULgqKJccrri2Ncc2iRxS73/89k+T/qcr82XbLKhH1PUlbScvaJQIDAQABAoIBAQCBVBo7HYKicvMXD3GqFmH+YL1BOQf8am7Sytl/rbcWnaxg+L/8w76z6VD/OgpCQJEuPDlMQI9EDc4Uh/339+l12EjafUbvaHOnntBvX8sFh6i6nbQXBEivhGsvT58ZGG2Cj6/UV0sWEsXLad0pyWwk24SM77Sun0baNF/Uk7kLVM9UoBxl7SO9EkEVTXrHxypUJKsSNdWpTgOzBP8OOe9V+C23TYbd8ge9fc13rYv9+DKGL7IFWPl5eSKvuvQRHymJBY8qYRjoB6+vg5eEYBuK3L4iGCoFDrb9bZB0s3jYddahahVjaql8/auQ4x10rGDvBv5l0Rd4IQ76Z4GI6urBAoGBAPaiFVED7r/WEvPv7ZpObuUc2n+fCVNP0V2CRbMOqgZV9/mH9BRh8S/kTKykXZJQTFyLdQf88YC/gz4uyppvL64xSb9v8eBuLSVlHBO7sdCe0J60bOCD66dETQJolnV/aDmdQJ9eyEah6p2pUn+2/MrwYAPlWv8GG/vWntdknYAVAoGBAOo2Rd+ia6g9nBy5Jr8CpjxcJNCo5qa4l2AH4XCtBY7rUEQsueXoVRtDusB1fi+wzw3koYHmzRNCOlP7Pp3b4aiBURJo8aZhp9vCDjQYKsRDqRB5MvqqLurzNQfX12UlFp8yux5wQLSexVExxR4Ka5JqyM0uQm5SGKR686EYdmXRAoGAFZYKF5UoiFHMRt1xBlhnDFaCKTlaL5iE0pJcV1epOfS8R3LGwiHwYiixLUsVhYDRrifmjlRtLr+UVRTzVqD0o2Jc+gxqNyJtgSBgXLnIzmGYEMvp25ywO2uW8ecFhDMqhCBnT4uUK9pwFmyDc/ooFfsD7FNeq4N1X41hSajcwCECgYBdMidvq5OnKIzcKRiv3QAE+K11kh/lq4IgpkIgsdiKXDxeRGXchCyJYtQHDTmfBH3/i0BxDkQAK9RF4q2x5vLOOFboOnYdvLIskfVQ6WTrH/lAItoUQG/W8dviORLunml4A5nNrOEyfhMKEgHbyE2xv2gTrRqmB2ji2kHjknZaYQKBgHmhkgnOcsSEQHocDXruFve1F+ns2qy7rQtwLoY7w2Y5KzsUHmn5jqwg1zccaGGe0LABBdonbY9Cr24X+tz+hMpAEX0qbGfBf2akl2cc4V4lP7f080cotU43eIMMq9bR1lrzJcocNPOU7qv5w1ZNN0RkE0i8Rjlh1Aw4OZruBx6V',
    #AES秘钥
    #'aes_private_key' => 'zAJrVygp2Zi0uJiwNIR6zA==',
    #异步通知地址
	'notify_url' => HTTP_SERVER . 'Front/Controller/User/AliPayNotify.php',
	#同步跳转
	'return_url' => HTTP_SERVER . $this->data['entrance'] . 'route=Front/User/Pay/pay_return',
	#编码格式
	'charset' => "UTF-8",
	#签名方式
	'sign_type' => 'RSA2',
	#支付宝网关
	'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
	#支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
	'alipay_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjIQYlwraUCsY9vKexiRfBHO9GpW1W8Ppl3WjFado0QYdhuqXcp3hW1YThfBKldpXnNko3ewB1S6VpF0KWMwunRmjiNBerHUtQZttD2Mx8UAYzHlHYt6RA2N9bmS/NrvTVt2rc1Yh1PKiK4KvRnlxTlCVOjNyO6ThQ9SxzGB3xYm7SacNDMFbVpRdajb2e0okLAUWQTTvI5EJmC1AaQYs/KHLd1xaXl9XCglJ6p1mmixqQSVolNUL1UtBixuIybdraPwxWYy2Tm1AZu9q68dDmfVyyQZf8eNIohiCWaIAB6dj6taBJRmKO0qZlxDe8FbsaSPhcpnJ8zTFOrzg0VlQAQIDAQAB',
];

