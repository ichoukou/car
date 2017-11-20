<?php
namespace Vender\Model\Reservation;

use Libs\Core\DbFactory AS DbFactory;

class Reservation extends DbFactory
{
    public function findReservationByReservationId($data)
    {
        $sql = "SELECT r.reservation_time,r.reservation_id,u.tel,u.numbering,uc.* FROM ".self::$dp."reservation AS r " .
            #" LEFT JOIN ".self::$dp."company AS cpy ON cpy.company_id=r.company_id ".
            " LEFT JOIN ".self::$dp."user AS u ON r.user_id=u.user_id ".
            " LEFT JOIN ".self::$dp."user_car AS uc ON r.car_id=uc.car_id ".
            " WHERE r.`deleted` = 1 AND r.`company_id` = :company_id AND r.`reservation_id` = :reservation_id";

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
               " WHERE r.`deleted` = 1 AND r.`company_id` = :company_id ";


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
            " WHERE r.`deleted` = 1 AND r.`company_id` = :company_id ";


//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function editUser($data)
    {
        $update_sql = "UPDATE " . self::$dp . "user SET"
            ." `user_group_id` = :user_group_id, `email` = :email, "
            ." `parent_name` = :parent_name, `tel` = :tel, `job` = :job "
            #." `newsletter` = :newsletter "
            ." WHERE `user_id` = :user_id";

        self::$db->update(
            $update_sql,
            [
                'user_group_id'     => $data['post']['user_group_id'],
                'parent_name'       => $data['post']['parent_name'],
                'tel'               => $data['post']['tel'],
                'job'               => $data['post']['job'],
                'email'             => $data['post']['email'],
                #'newsletter'        => $data['post']['newsletter'],
                'user_id'           => $data['post']['user_id'],
            ]
        );
    }
}
