<?php
namespace Front\Controller\User;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

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

    public function pay()
    {
        if ($info = $this->validate_pay()) {
            if (empty($info))
                exit("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}");

            $config = [];

            require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'wappay'.DS.'service'.DS.'AlipayTradeService.php';
            require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'wappay'.DS.'buildermodel/AlipayTradeWapPayContentBuilder.php';
            require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'/config.php';

            #商户订单号，商户网站订单系统中唯一订单号，必填
            $out_trade_no = $info['bill'];
            #订单名称，必填
            $subject = '用户车辆维修结算支付';
            #付款金额，必填
            $total_amount = $info['total_revenue'];
            #商品描述，可空
            $body = '用户车辆维修结算支付';
            #超时时间
            $timeout_express="1m";

            $payRequestBuilder = new \AlipayTradeWapPayContentBuilder();
            $payRequestBuilder->setBody($body);
            $payRequestBuilder->setSubject($subject);
            $payRequestBuilder->setOutTradeNo($out_trade_no);
            $payRequestBuilder->setTotalAmount($total_amount);
            $payRequestBuilder->setTimeExpress($timeout_express);

            $payResponse = new \AlipayTradeService($config);
            $result=$payResponse->wapPay($payRequestBuilder, $config['return_url'], $config['notify_url']);
            var_Dump($result);
            #M::Front('User\\Reservation', 'editReservation', ['post'=>$post]);

            #exit(json_encode(['status'=>1, 'result'=>'修改预约信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 支付异步回调消息
     */
    public function pay_notify()
    {
        var_dump('aaaaa');
    }

    /**
     * 支付返回结果
     */
    public function pay_return()
    {
        var_dump('bbbbb');
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