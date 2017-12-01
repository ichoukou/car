<?php

/**
 * 支付宝异步通知类
 * Class AliPayNotify
 */
class AliPayNotify
{
    public $data = [];
    public $_config = [];

    public function __construct()
    {
        $_config = [];
        $config = [];

        require_once '../../../Libs/Configs/Config.php';
        require_once '../../../Libs/Core/Log.php';
        require_once '../../../Libs/Db/PdoMySQL.php';
        require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'config.php';
        require_once ROOT_PATH.'Libs'.DS.'ExtendsClass'.DS.'Alipay'.DS.'wappay'.DS.'service'.DS.'AlipayTradeService.php';

        $this->data['entrance'] = 'index.php?';
        $this->_config = $_config;

        $db = new \Libs\Db\PdoMySQL($_config['DB']['true']);
        Libs\Core\Log::$conf = $_config['log'];

        if (!empty($_POST)) {
            Libs\Core\Log::wirte_other_info(json_encode($_POST, JSON_UNESCAPED_UNICODE));
        }

        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_POST,true));
        $result = $alipaySevice->check($_POST);

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

        $return = [];
        $return['pay_type'] = '支付宝'; #支付类型
        $return['trade_no'] = htmlspecialchars($_POST['trade_no']);
        $return['bill'] = htmlspecialchars($_POST['out_trade_no']);
        $return['notify_id'] = htmlspecialchars($_POST['notify_id']);
        $return['total_amount'] = (float)htmlspecialchars($_POST['total_amount']);
        $return['receipt_amount'] = (float)htmlspecialchars($_POST['receipt_amount']);
        $return['app_id'] = htmlspecialchars($_POST['app_id']);
        $return['buyer_id'] = htmlspecialchars($_POST['buyer_id']);
        $return['buyer_logon_id'] = htmlspecialchars($_POST['buyer_logon_id']);
        $return['seller_id'] = htmlspecialchars($_POST['seller_id']);
        $return['seller_email'] = htmlspecialchars($_POST['seller_email']);
        $return['gmt_create'] = htmlspecialchars($_POST['gmt_create']);
        $return['gmt_payment'] = htmlspecialchars($_POST['gmt_payment']);
        $return['gmt_refund'] = htmlspecialchars($_POST['gmt_refund']);
        $return['gmt_close'] = htmlspecialchars($_POST['gmt_close']);
        $return['notify_time'] = htmlspecialchars($_POST['notify_time']);
        $return['notify_type'] = 2; #异步通知
        $return['trade_status'] = $_POST['trade_status'];
        $return['notify_message'] = $status_config[$_POST['trade_status']];

        $bill_info = $this->findBillInfo($return, $db, $_config['DB']['true']);
        $return['reservation_id'] = $bill_info['reservation_id'];

        if (empty($bill_info)){
            $return['message'] = '没有匹配到对应的订单';
        } elseif ($bill_info['status'] == 3) {
            if($_POST['trade_status'] == 'TRADE_FINISHED' or $_POST['trade_status'] == 'TRADE_SUCCESS') {
                #TRADE_FINISHED 退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                #TRADE_SUCCESS 付款完成后，支付宝系统发送该交易状态通知
                $return['reservation_status'] = 4;
                $this->editReservation($return, $db, $_config['DB']['true']);
                $return['message'] = '订单状态修改为已付款，异步通知状态是交易支付成功或者交易结束';
            } else {
                $return['message'] = '订单状态为未付款，但是异步通知并非是交易支付成功或者交易结束';
            }
        } else {
            $return['message'] = '订单状态并非为未支付';
        }

        $this->addPaylog($return, $db, $_config['DB']['true']);

        if($result) {//验证成功
            echo "success";		#请不要修改或删除
        }else {
            //验证失败
            echo "fail";	#请不要修改或删除
        }
    }

    public function findBillInfo($data, $db, $_config)
    {
        $sql = "SELECT r.reservation_id,r.bill,r.status,mc.total_revenue FROM " . $_config['db_prefix'] . "reservation AS r " .
            " LEFT JOIN " . $_config['db_prefix'] . "maintenance_costs AS mc ON mc.reservation_id=r.reservation_id ".
            " WHERE r.`deleted` = 1 AND r.`bill` = :bill AND mc.`total_revenue` = :total_amount";

        return $db->get_one(
            $sql,
            [
                'bill'=>$data['bill'],
                'total_amount'=>$data['total_amount'],
            ]
        );
    }

    public function editReservation($data, $db, $_config)
    {
        $conditions = [
            'status' => $data['reservation_status'],
            'pay_type' => $data['pay_type'],
            'reservation_id' => $data['reservation_id']
        ];
        #如果处于待支付状态 status = 3，则修改状态
        $update_sql = " UPDATE " . $_config['db_prefix'] . "reservation SET " .
            " status = :status, pay_type = :pay_type " .
            " WHERE `reservation_id` = :reservation_id AND `status` = 3";

        $db->update($update_sql, $conditions);
    }

    public function addPaylog($data, $db, $_config)
    {
        $sql = "INSERT INTO " . $_config['db_prefix'] . "pay_log " .
            "(`reservation_id`,`pay_type`,`bill`,`total_amount`,`receipt_amount`,`notify_type`,`notify_message`,`app_id`, " .
            "`trade_no`,`seller_id`,`seller_email`,`notify_time`,`notify_id`,`buyer_id`,`buyer_logon_id`, " .
            "`gmt_create`,`gmt_payment`,`gmt_refund`,`gmt_close`,`trade_status`,`message` ) " .
            " VALUES ";

        return $db->insert(
            $sql,
            [
                $data['reservation_id'],
                $data['pay_type'],
                $data['bill'],
                $data['total_amount'],
                $data['receipt_amount'],
                $data['notify_type'],
                $data['notify_message'],
                $data['app_id'],
                $data['seller_id'],
                $data['seller_email'],
                $data['trade_no'],
                $data['notify_time'],
                $data['notify_id'],
                $data['buyer_id'],
                $data['buyer_logon_id'],
                $data['gmt_create'],
                $data['gmt_payment'],
                $data['gmt_refund'],
                $data['gmt_close'],
                $data['trade_status'],
                $data['message']
            ]
        );
    }
}

new AliPayNotify();