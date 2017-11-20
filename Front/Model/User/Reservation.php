<?php
namespace Front\Model\User;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;

class Reservation extends DbFactory
{
    public function findReservationByReservationId($data)
    {
        $sql = "SELECT r.*,cpy.name,cpy.score,cpy.score_count,cpy.address,uc.plate_number,uc.brand_type FROM ".self::$dp."reservation AS r " .
            " LEFT JOIN ".self::$dp."company AS cpy ON cpy.company_id=r.company_id ".
            #" LEFT JOIN ".self::$dp."user AS u ON r.user_id=u.user_id ".
            " LEFT JOIN ".self::$dp."user_car AS uc ON r.car_id=uc.car_id ".
            " WHERE r.`deleted` = 1 AND r.`user_id` = :user_id AND r.`reservation_id` = :reservation_id";

        return self::$db->get_one($sql, ['reservation_id'=>$data['reservation_id'],'user_id'=>$_SESSION['user_id']]);
    }

    public function findReservations($data)
    {
        $params = $data['params'];

        $conditions = [
            'start'         => $params['start'],
            'limit'         => $params['limit'],
            'user_id'       => $_SESSION['user_id']
        ];

        $sql = "SELECT r.*,cpy.name,uc.plate_number,uc.brand_type FROM ".self::$dp."reservation AS r " .
               " LEFT JOIN ".self::$dp."company AS cpy ON cpy.company_id=r.company_id ".
               #" LEFT JOIN ".self::$dp."user AS u ON r.user_id=u.user_id ".
               " LEFT JOIN ".self::$dp."user_car AS uc ON r.car_id=uc.car_id ".
               " WHERE r.`deleted` = 1 AND r.`user_id` = :user_id ";

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
            'user_id'       => $_SESSION['user_id']
        ];

        $sql = "SELECT  COUNT(*) AS total FROM ".self::$dp."reservation AS r " .
            " LEFT JOIN ".self::$dp."company AS cpy ON cpy.company_id=r.company_id ".
            #" LEFT JOIN ".self::$dp."user AS u ON r.user_id=u.user_id ".
            " LEFT JOIN ".self::$dp."user_car AS uc ON r.car_id=uc.car_id ".
            " WHERE r.`deleted` = 1 AND r.`user_id` = :user_id ";

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
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

    public function removeCompany($data)
    {
        $sql = "UPDATE ".self::$dp."company SET `deleted`=2 WHERE `company_id` = :company_id";

        return self::$db->update($sql, ['company_id'=>$data['company_id']]);
    }

    public function removeCompanys($data)
    {
        $sql = "UPDATE ".self::$dp."company SET `deleted`=2 WHERE `company_id` = :company_id";

        foreach ($data['company_ids'] as $company_id) {
            self::$db->update($sql, ['company_id'=>$company_id]);
        }

        return true;
    }
}
