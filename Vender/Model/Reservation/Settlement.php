<?php
namespace Vender\Model\Reservation;

use Libs\Core\DbFactory AS DbFactory;

class Settlement extends DbFactory
{
    public function findReservationByReservationId($data)
    {
        $sql = "SELECT mc.* FROM ".self::$dp."reservation AS r " .
            " LEFT JOIN ".self::$dp."maintenance_costs AS mc ON mc.reservation_id=r.reservation_id ".
            " WHERE r.`deleted` = 1 AND (r.`status` = 3 OR r.`status` = 4) AND r.`company_id` = :company_id AND r.`reservation_id` = :reservation_id";

        return self::$db->get_one($sql, ['reservation_id'=>$data['reservation_id'],'company_id'=>$_SESSION['company_id']]);
    }

    public function findReservations($data)
    {
        $params = $data['params'];

        $conditions = [
            'start'      => $params['start'],
            'limit'      => $params['limit'],
            'company_id' => $_SESSION['company_id']
        ];

        $sql = "SELECT r.*,u.tel,uc.plate_number,uc.car_type FROM ".self::$dp."reservation AS r " .
               " LEFT JOIN ".self::$dp."user AS u ON r.user_id = u.user_id ".
               " LEFT JOIN ".self::$dp."user_car AS uc ON r.car_id = uc.car_id ".
               " WHERE r.`deleted` = 1 AND (r.`status` = 3 OR r.`status` = 4) AND r.`company_id` = :company_id ";


//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY r.reservation_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findReservationsCount($data)
    {
        $params = $data['params'];

        $conditions = [
            'company_id' => $_SESSION['company_id']
        ];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."reservation AS r " .
            " LEFT JOIN ".self::$dp."user AS u ON r.user_id = u.user_id ".
            " LEFT JOIN ".self::$dp."user_car AS uc ON r.car_id = uc.car_id ".
            " WHERE r.`deleted` = 1 AND (r.`status` = 3 OR r.`status` = 4) AND r.`company_id` = :company_id ";


//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }
}
