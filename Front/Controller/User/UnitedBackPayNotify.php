<?php

/**
 * 支付宝异步通知类
 * Class AliPayNotify
 */
class UnitedBackPayNotify
{
    public $data = [];
    public $_config = [];

    public function __construct()
    {
        $_config = [];

        require_once '../../../Libs/Configs/Config.php';
        require_once '../../../Libs/Core/Log.php';
        require_once '../../../Libs/Db/PdoMySQL.php';

        $this->data['entrance'] = 'index.php?';
        $this->_config = $_config;

        $db = new \Libs\Db\PdoMySQL($_config['DB']['true']);
        Libs\Core\Log::$conf = $_config['log'];

        if (!empty($_GET)) {
            Libs\Core\Log::wirte_other_info(json_encode($_GET, JSON_UNESCAPED_UNICODE));
        }

        $return = [];
        $key = 'eybEZxPXqp2dae62TYAfFVyB46rtOMBCj1iIlMnzjdTBXPUdYeUsPXvM2N1fibKwU5KstuIUMFw8BgDiOIMYjJxvFauWR3CYvjOD0zGzFKuezVHTmTtHZBORAZjyM3Yg';
        $return['pay_type'] = '联行支付'; #支付类型
        $return['bill'] = htmlspecialchars($_GET['dealOrder']); #商户订单号
        $return['total_amount'] = (float)htmlspecialchars($_GET['dealFee']); #订单金额
        $return['trade_status'] = $_GET['dealState']; #订单支付状态
        $return['notify_time'] = date('Y-m-d H:i:s', time()); #消息返回时间
        $return['notify_type'] = 2; #异步通知

        $bill_info = $this->findBillInfo($return, $db, $_config['DB']['true']);
        $return['reservation_id'] = $bill_info['reservation_id'];

        if (empty($bill_info)){
            $return['message'] = '没有匹配到对应的订单';
        } elseif ($bill_info['status'] == 3) {
            if($_GET['dealState'] == 'SUCCESS') {
                $return['reservation_status'] = 4;
                $this->editReservation($return, $db, $_config['DB']['true']);
                $return['message'] = '订单状态修改为已付款，异步通知状态是交易支付成功';
            } else {
                $return['message'] = '订单状态为未付款，但是异步通知并非是交易支付成功';
            }
        } else {
            $return['message'] = '订单状态并非为未支付';
        }

        $this->addPaylog($return, $db, $_config['DB']['true']);

        echo 'notify_success';
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

new UnitedBackPayNotify();