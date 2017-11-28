<?php
class AliPayNotify
{
    public $data = [];

    public function __construct()
    {
        $_config = [];
        $config = [];
        require_once '../../../Libs/Configs/Config.php';
        require_once '../../../Libs/Core/Log.php';
        #require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'config.php';
        #require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'wappay'.DS.'service'.DS.'AlipayTradeService.php';
        $this->data['entrance'] = 'index.php?';

        Libs\Core\Log::$conf = $_config['log'];

        if (!empty($_POST)) {
            Libs\Core\Log::wirte_other_info(json_encode($_POST, JSON_UNESCAPED_UNICODE));
        }
        
        $info = [
            'gmt_create' => '2017-11-28 23:11:48',
            'charset' => 'UTF-8',
            'seller_email' => '13361076388',
            'subject' => '用户车辆维修结算支付',
            'sign' => '用户车辆维修结算支付',
            'sign' => 'IMMsQQUPejUxVhi4vSyLFhY4hf\/CCEKoVoKMUPc+bX\/sWIcpYi7BBdU+N07uXef9\/SzvGbzQOaY4gd3Q2R5IqWSWXfa+Y4P+k7VW7Fy5ZD+ujwy9cuTVRzCigGzWptOF6rQx\/C3ZS742Tz+0egpcsbI5umDj7FrQ4QBalHqOzHrRqvQnfF5eCs5naR4+RThPZpvlcMrsGyNXihLYbCWgt0N2pkVOLr9oElv+qrHCaNh0AE\/nX0ranWrRll8uZsDc0y8VSr6IJN34WPiChuvtxIQLbbTgO9U041I75dvHGFpGOkiIGIKfyRMVAHPX6\/J83NJkaMWHTLSqGKU4G3H2gg==',
            'body' => '用户车辆维修结算支付',
            'buyer_id' => '2088602220435934',
            'invoice_amount' => '0.01',
            'notify_id' => '14c15fc89975dac9094d88179e69061n6h',
            'fund_bill_list' => [
                'amount' => 0.01,
                'fundChannel' => 'ALIPAYACCOUNT'
            ],
            'notify_type' => 'trade_status_sync',
            'trade_status' => 'TRADE_SUCCESS',
            'receipt_amount' => '0.01',
            'app_id' => '2017111800028220',
            'buyer_pay_amount' => '0.01',
            'sign_type' => 'RSA2',
            'seller_id' => '2088902349621211',
            'gmt_payment' => '2017-11-28 23:11:49',
            'notify_time' => '2017-11-28 23:11:49',
            'version' => '1.0',
            'out_trade_no' => '20171127140323GB2762603176989150',
            'total_amount' => '0.01',
            'trade_no' => '2017112821001004930597899507',
            'auth_app_id' => '2017111800028220',
            'buyer_logon_id' => '136****0135',
            'point_amount' => '0.00',
        ];
        $arr=$_POST;
        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($arr);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */

        $status_config = [
            'WAIT_BUYER_PAY'=>'交易创建，等待买家付款',
            'TRADE_CLOSED'=>'未付款交易超时关闭，或支付完成后全额退款',
            'TRADE_SUCCESS'=>'交易支付成功',
            'TRADE_FINISHED'=>'交易结束，不可退款'
        ];

        if($result) {//验证成功
            #请在这里加上商户的业务逻辑程序代
            #——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            #获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            #商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            #支付宝交易号
            $trade_no = $_POST['trade_no'];
            #交易状态
            $trade_status = $_POST['trade_status'];

            if($_POST['trade_status'] == 'TRADE_FINISHED') {

                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
            } elseif ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";		//请不要修改或删除

        }else {
            //验证失败
            echo "fail";	//请不要修改或删除

        }
    }
}

new AliPayNotify();