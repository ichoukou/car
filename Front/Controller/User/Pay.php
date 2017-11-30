<?php
namespace Front\Controller\User;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\WxCommon as WC;
use Libs\Core\Log as Log;

class Pay extends Controller
{
    public function index()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['reservation_id']);

        $reservation_id = (int)$_GET['reservation_id'];
        if (empty($reservation_id))
            exit(header("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}"));

        $info = M::Front('User\\Pay', 'findReservationByReservationId', ['reservation_id'=>$reservation_id]);
        if (empty($info))
            exit(header("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}"));

        $this->data['reservation_info'] = $info;

        $this->create_page();

        L::output(L::view('User\\PayIndex', 'Front', $this->data));
    }


    /**
     * 支付宝支付调用
     */
    public function ali_pay()
    {
        #https://docs.open.alipay.com/203 官方文档
        #https://www.cnblogs.com/phpxuetang/p/5656266.html 缺少php_openssl.so的安装方法
        #https://openclub.alipay.com/read.php?tid=2684&fid=40 私钥错误

        #https://open.alipay.com/search/searchDetail.htm?tabType=support&keyword=missing-signature-config 官方错误解答集合
        #https://tech.open.alipay.com/support/knowledge/index.htm?knowledgeId=201602068952&categoryId=20057#/?_k=n1ntam  商户config配置错误
        #https://openclub.alipay.com/read.php?tid=362&fid=2 别人的DEMO
        if ($info = $this->validate_pay()) {
            header("Content-type: text/html; charset=utf-8");

            if (empty($info))
                exit("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}");

            $config = [];
            require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'wappay'.DS.'service'.DS.'AlipayTradeService.php';
            require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'wappay'.DS.'buildermodel/AlipayTradeWapPayContentBuilder.php';
            require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'/config.php';

            #商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $info['bill'];
            #订单名称，必填
            $subject = '支付宝用户车辆维修结算支付';
            #付款金额，必填
            $total_amount = $info['total_revenue'];
            #商品描述，可空
            $body = '支付宝用户车辆维修结算支付';
            #超时时间
            $timeout_express="1m";

            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);
            $payResponse = new \AlipayTradeService($config);
            $payResponse->wapPay($payRequestBuilder, $config['return_url'], $config['notify_url']);
        }
    }

    /**
     * 功能：支付宝页面跳转同步通知页面
     * 版本：2.0
     * 修改日期：2016-11-01
     * 说明：
     * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

     *************************页面功能说明*************************
     * 该页面可在本机电脑测试
     * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
     */
    public function pay_return()
    {
        $config = [];
        require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'config.php';
        require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'wappay'.DS.'service'.DS.'AlipayTradeService.php';

        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($_GET);

        /* 实际验证过程建议商户添加以下校验。
        1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
        2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
        3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
        4、验证app_id是否为该商户本身。
        */

        $return = [];
        $return['trade_no'] = htmlspecialchars($_GET['trade_no']); #支付宝交易凭证号
        $return['bill'] = htmlspecialchars($_GET['out_trade_no']); #商户订单号
        $return['total_amount'] = (float)htmlspecialchars($_GET['total_amount']); #订单金额
        $return['app_id'] = htmlspecialchars($_GET['auth_app_id']); #商户APPID
        $return['seller_id'] = htmlspecialchars($_GET['seller_id']); #商户支付宝用户号
        $return['notify_time'] = htmlspecialchars($_GET['timestamp']); #消息返回时间
        $return['notify_type'] = 1; #同步通知

        $bill_info = M::Front('User\\Pay', 'findBillInfo', $return);
        if ($bill_info['status'] == 4 or $bill_info['status'] == 5) {
            exit(header("location:{$this->data['entrance']}route=Front/User/Pay/pay_success&reservation_id={$bill_info['reservation_id']}"));
        } elseif ($bill_info['status'] == 6 or empty($bill_info)) {
            exit(header("location:{$this->data['entrance']}route=Front/User/Pay/pay_error&reservation_id={$bill_info['reservation_id']}"));
        }

        $return['reservation_id'] = $bill_info['reservation_id'];
        $return['reservation_status'] = 6; #对应 $reservation_status  支付状态

        if (empty($return['trade_no']) or empty($return['bill']) or empty($return['app_id']) or empty($return['seller_id'])) {
            $return['message'] = '参数缺少';
        } elseif (empty($bill_info)) {
            $return['message'] = '未匹配到订单信息';
        } elseif($return['app_id'] != $config['app_id']) {
            $return['message'] = '开发者的应用Id匹配错误';
        } else {
            $return['message'] = '交易成功';
            $return['reservation_status'] = 4;
        }

        M::Front('User\\Pay', 'addPaylog', $return);
        M::Front('User\\Pay', 'editReservation', $return);

        if ($return['reservation_status'] == 4) {
            exit(header("location:{$this->data['entrance']}route=Front/User/Pay/pay_success&reservation_id={$bill_info['reservation_id']}"));
        } else {
            exit(header("location:{$this->data['entrance']}route=Front/User/Pay/pay_error&reservation_id={$bill_info['reservation_id']}"));
        }

//        if (!$result) { #验证成功
//        } else {
//            //验证失败
//            echo "验证失败";
//        }
    }

    /**
     * 功能：支付宝服务器异步通知页面
     * 版本：2.0
     * 修改日期：2016-11-01
     * 说明：
     * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

     *************************页面功能说明*************************
     * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
     * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
     * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
     */
    public function pay_notify()
    {
        #具体业务逻辑在同目录下的 XxxPayNotify.php,Xxx为支付名称
    }

    /**
     * 联行支付支付调用
     */
    public function united_bank_pay()
    {
        if ($info = $this->validate_pay()) {
            header("Content-type: text/html; charset=utf-8");
            if (empty($info))
                exit("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}");

            $bank_arr = [
                'BOCW',     #中国银行wap
                'CCBW',     #建设银行wap信用卡
                'CCBWZ',    #建设银行wap借记卡
                'CMBW',     #招商银行wap
                'ICBCW',    #工商银行wap
                'COMMW',    #交通银行wap
                'CEBW',     #光大银行wap
                'SPDBW',    #浦发银行wap
                'ABCW',     #农业银行wap
            ];

            $merId      = '699851';
            $dealOrder  = $info['bill'];
            $dealFee    = $info['total_revenue'];
            $dealReturn = HTTP_SERVER . $this->data['entrance'] . 'route=Front/User/Pay/united_back_pay_return';
            $dealNotify = HTTP_SERVER . 'Front/Controller/User/UnitedBackPayNotify.php';
            $dealName   = '支付宝用户车辆维修结算支付';
            $dealBank   = 'CMBW';
            $dealHeader = 'false';
            $key        = 'eybEZxPXqp2dae62TYAfFVyB46rtOMBCj1iIlMnzjdTBXPUdYeUsPXvM2N1fibKwU5KstuIUMFw8BgDiOIMYjJxvFauWR3CYvjOD0zGzFKuezVHTmTtHZBORAZjyM3Yg';
            #2 生成 Data
            $Data       = $merId . $dealOrder . $dealFee . $dealReturn;
            #3 生成 dealSignure
            $dealSignure = sha1($Data.$key);

            //获得表单传过来的数据
            $def_url  = '<br />';
            $def_url  = '<form method="post" action="http://user.sdecpay.com/paygate.html" style="display: none;">';
            $def_url .= '	<input type = "hidden" name = "merId"	    value = "'.$merId.'">';
            $def_url .= '	<input type = "hidden" name = "dealName"    value = "'.$dealName.'">';
            $def_url .= '	<input type = "hidden" name = "dealOrder" 	value = "'.$dealOrder.'">';
            $def_url .= '	<input type = "hidden" name = "dealFee" 	value = "'.$dealFee.'">';
            $def_url .= '	<input type = "hidden" name = "dealBank" 	value = "'.$dealBank.'">';
            $def_url .= '	<input type = "hidden" name = "header" 	    value = "'.$dealHeader.'">';
            $def_url .= '	<input type = "hidden" name = "dealSignure"	value = "'.$dealSignure.'">';
            $def_url .= '	<input type = "hidden" name = "dealReturn"	value = "'.$dealReturn.'">';
            $def_url .= '	<input type = "hidden" name = "dealNotify"	value = "'.$dealNotify.'">';
            $def_url .= '	<input type=submit value="立即付款">';
            $def_url .= '</form>';

            var_dump($def_url);


            exit;
        }
    }

    public function pay_success()
    {
        $this->create_page();

        $reservation_id = (int)$_GET['reservation_id'];
        $bill_info = M::Front('User\\Pay', 'findBillStatus', ['reservation_id'=>$reservation_id]);

        if (empty($bill_info) or $bill_info['status'] != 4) {
            $this->data['notify_message'] = '您已评价';
        } else {
            $this->data['notify_message'] = '恭喜您本次支付成功';
        }

        if ($bill_info['status'] == 6) {
            exit(header("location:{$this->data['entrance']}route=Front/User/Pay/pay_error&reservation_id={$bill_info['reservation_id']}"));
        }

        $this->data['bill_info'] = $bill_info;
        $this->data['reservation_id'] = $reservation_id;

        L::output(L::view('User\\PaySuccess', 'Front', $this->data));
    }

    public function pay_error()
    {
        $this->create_page();

        $this->data['notify_message'] = '支付异常，请联系管理员';

        L::output(L::view('User\\PayError', 'Front', $this->data));
    }

    public function validate_pay()
    {
        $this->is_login();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);

            if (empty($post['reservation_id'])) {
                $info = '';
            } else {
                $info = M::Front('User\\Pay', 'findReservationByReservationId', ['reservation_id'=>$post['reservation_id']]);
                if (empty($info)) {
                    $info = '';
                }
            }

            return $info;
        } else {
            return false;
        }
    }
}