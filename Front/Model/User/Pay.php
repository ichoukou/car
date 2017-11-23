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

    public function editReservation($data)
    {
        $conditions = [
            'reservation_id' => $data['post']['reservation_id'],
            'reservation_time' => $data['post']['reservation_time']
        ];

        $update_sql = " UPDATE " . self::$dp . "reservation SET " .
                      " reservation_time = :reservation_time " .
                      " WHERE `reservation_id` = :reservation_id";

        self::$db->update($update_sql, $conditions);
    }
}
