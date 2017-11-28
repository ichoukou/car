<?php
namespace Front\Model\User;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;

class Pay extends DbFactory
{
    public function findReservationByReservationId($data)
    {
        $sql = "SELECT r.*,mc.* FROM ".self::$dp."reservation AS r " .
            " LEFT JOIN ".self::$dp."maintenance_costs AS mc ON mc.reservation_id=r.reservation_id ".
            #" LEFT JOIN ".self::$dp."user AS u ON r.user_id=u.user_id ".
            " WHERE r.`deleted` = 1 AND r.`status` = 3 AND r.`user_id` = :user_id AND r.`reservation_id` = :reservation_id";

        return self::$db->get_one($sql, ['reservation_id'=>$data['reservation_id'],'user_id'=>$_SESSION['user_id']]);
    }

    public function findBillInfo($data)
    {
        $sql = "SELECT r.reservation_id,r.bill,r.status,mc.total_revenue FROM ".self::$dp."reservation AS r " .
            " LEFT JOIN ".self::$dp."maintenance_costs AS mc ON mc.reservation_id=r.reservation_id ".
            " WHERE r.`deleted` = 1 AND r.`user_id` = :user_id AND r.`bill` = :bill AND mc.`total_revenue` = :total_amount";

        return self::$db->get_one(
            $sql,
            [
                'bill'=>$data['bill'],
                'total_amount'=>$data['total_amount'],
                'user_id'=>$_SESSION['user_id']
            ]
        );
    }

    public function addPaylog($data)
    {
        $sql = "INSERT INTO ".self::$dp."pay_log (`reservation_id`,`bill`,`total_amount`,`notify_type`,`notify_message`,`app_id`,`trade_no`,`seller_id`,`notify_time`) VALUES ";

        return self::$db->insert(
            $sql,
            [
                $data['reservation_id'],
                $data['bill'],
                $data['total_amount'],
                $data['notify_type'],
                $data['notify_message'],
                $data['app_id'],
                $data['seller_id'],
                $data['trade_no'],
                $data['notify_time']
            ]
        );
    }

    public function editReservation($data)
    {
        $conditions = [
            'status' => $data['reservation_status'],
            'reservation_id' => $data['reservation_id']
        ];
        #如果处于待支付状态 status = 3，则修改状态
        $update_sql = " UPDATE " . self::$dp . "reservation SET " .
            " status = :status " .
            " WHERE `reservation_id` = :reservation_id AND `status` = 3";

        self::$db->update($update_sql, $conditions);
    }

    public function findBillStatus($data)
    {
        $sql = "SELECT reservation_id,status FROM ".self::$dp."reservation " .
            " WHERE `deleted` = 1 AND `reservation_id` = :reservation_id";

        return self::$db->get_one(
            $sql,
            [
                'reservation_id'=>$data['reservation_id']
            ]
        );
    }
}
