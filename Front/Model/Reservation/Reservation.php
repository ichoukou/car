<?php
namespace Front\Model\Reservation;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;

class Reservation extends DbFactory
{
    public function findReservationByCompanyId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."company WHERE `company_id` = :company_id";
        $info = self::$db->get_one($sql, ['company_id'=>$data['company_id']]);

        if(empty($info))
            return '';

        $update_sql = "UPDATE ".self::$dp."company SET `views` = views + 1 WHERE `company_id` = :company_id";
        self::$db->update($update_sql, ['company_id'=>$data['company_id']]);

        return $info;
    }

    public function findReservations($data)
    {
        $params = $data['params'];

        $conditions = [
            'start'         => $params['start'],
            'limit'         => $params['limit']
        ];

        $sql = "SELECT * FROM ".self::$dp."company WHERE `deleted` = 1 ";

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY company_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findReservationsCount($data)
    {
        $params = $data['params'];

        $conditions = [
        ];

        $sql = "SELECT  COUNT(*) AS total FROM ".self::$dp."company WHERE `deleted` = 1 ";

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addReservation($data)
    {
        $find_sql = "SELECT u.user_id,uc.car_id FROM ".self::$dp."user AS u LEFT JOIN ".self::$dp."user_car AS uc ON u.user_id = uc.user_id WHERE u.`user_id` = :user_id";
        $user_info = self::$db->get_one($find_sql, ['user_id'=>$_SESSION['user_id']]);

        $sql = "INSERT INTO ".self::$dp."reservation (`company_id`,`user_id`,`car_id`,`reservation_time`) VALUES ";
        $reservation = self::$db->insert(
            $sql,
            [
                $data['post']['company_id'],
                $user_info['user_id'],
                $user_info['car_id'],
                $data['post']['reservation_time']
            ]
        );

        return $reservation;
    }
}
