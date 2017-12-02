<?php
namespace Vender\Model\Reservation;

use Libs\Core\DbFactory AS DbFactory;

class Reservation extends DbFactory
{
    public function findReservationByReservationId($data)
    {
        $sql = "SELECT r.description as r_description,r.reservation_time,r.reservation_id,u.tel,u.numbering,uc.* FROM ".self::$dp."reservation AS r " .
            #" LEFT JOIN ".self::$dp."company AS cpy ON cpy.company_id=r.company_id ".
            " LEFT JOIN ".self::$dp."user AS u ON r.user_id=u.user_id ".
            " LEFT JOIN ".self::$dp."user_car AS uc ON r.car_id=uc.car_id ".
            " WHERE r.`deleted` = 1 AND (r.`status` = 1 OR r.`status` = 2) AND r.`company_id` = :company_id AND r.`reservation_id` = :reservation_id";

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

        $sql = "SELECT r.*,u.tel,uc.plate_number,uc.car_type,uc.owner FROM ".self::$dp."reservation AS r " .
               " LEFT JOIN ".self::$dp."user AS u ON r.user_id = u.user_id ".
               " LEFT JOIN ".self::$dp."user_car AS uc ON r.car_id = uc.car_id ".
               " WHERE r.`deleted` = 1 AND (r.`status` = 1 OR r.`status` = 2) AND r.`company_id` = :company_id ";


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
            " WHERE r.`deleted` = 1 AND (r.`status` = 1 OR r.`status` = 2) AND r.`company_id` = :company_id ";


//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function editReservation($data)
    {
        $update_sql = "UPDATE " . self::$dp . "reservation SET"
            ." `image_path` = :image_path, `audio_path` = :audio_path, `video_path` = :video_path, `status` = :status WHERE `reservation_id` = :reservation_id AND `company_id` = :company_id ";

        self::$db->update(
            $update_sql,
            [
                'image_path'     => $data['post']['image_path'],
                'audio_path'     => $data['post']['audio_path'],
                'video_path'     => $data['post']['video_path'],
                'status'         => 2,
                'reservation_id' => $data['post']['reservation_id'],
                'company_id'     => $_SESSION['company_id']
            ]
        );
    }

    public function addSettlement($data)
    {
        $conditions = [
            'company_id'     => $_SESSION['company_id'],
            'reservation_id' => $data['post']['reservation_id'],
        ];

        $get_sql = "SELECT reservation_id FROM ".self::$dp."reservation WHERE `deleted` = 1 AND `status` = 2 AND `reservation_id` = :reservation_id AND `company_id` = :company_id ";

        $info = self::$db->get_one($get_sql, $conditions);

        if (empty($info['reservation_id']))
            return -1;

        $insert_sql = "INSERT INTO " . self::$dp . "maintenance_costs " .
            " (`image_path`,`total_revenue`, `reservation_id`) VALUES ";

        self::$db->insert(
            $insert_sql,
            [
                $data['post']['image_path'],
                (float)$data['post']['total_revenue'],
                $data['post']['reservation_id']
            ]
        );

        $update_sql = "UPDATE " . self::$dp . "reservation SET"
            ." `status` = :status WHERE `reservation_id` = :reservation_id AND `company_id` = :company_id ";

        self::$db->update(
            $update_sql,
            [
                'status'            => 3,
                'reservation_id'    => $data['post']['reservation_id'],
                'company_id'        => $_SESSION['company_id']
            ]
        );

        return 1;
    }
}
