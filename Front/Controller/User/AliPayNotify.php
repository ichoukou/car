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
        require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'config.php';
        require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'wappay'.DS.'service'.DS.'AlipayTradeService.php';
        $this->data['entrance'] = 'index.php?';

        Libs\Core\Log::$conf = $_config['log'];
        Libs\Core\Log::wirte_other_info('aaaaaaaaaa');

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
//        $result = true;
//        $_POST['out_trade_no'] = '20171123034843GB230892322879313';
//        $_POST['trade_no'] = '';
//        $_POST['appid'] = '2017111800028220';
//        log::wirte_other_info(json_encode("RESULT:$result", JSON_UNESCAPED_UNICODE));
//        log::wirte_other_info(json_encode("POST:{$_POST}", JSON_UNESCAPED_UNICODE));
//        log::wirte_other_info(json_encode("----------------------------", JSON_UNESCAPED_UNICODE));
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