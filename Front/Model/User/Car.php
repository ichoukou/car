<?php
namespace Front\Model\User;

use Libs\Core\DbFactory AS DbFactory;

class Car extends DbFactory
{
    public function findCarByCarId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user_car WHERE `car_id` = :car_id";

        return self::$db->get_one($sql, ['car_id'=>$data['car_id']]);
    }

    public function findCarBUserId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user_car WHERE `user_id` = :user_id";

        return self::$db->get_one($sql, ['user_id'=>$_SESSION['user_id']]);
    }

    public function findCars($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit'],
            'user_id' => $_SESSION['user_id']
        ];

        $sql = "SELECT * FROM ".self::$dp."user_car WHERE `deleted` = 1 AND `user_Id` = :user_id ";

//        if (!empty($params['filter_parent_name'])) {
//            $sql .= "AND `parent_name` LIKE :parent_name ";
//            $conditions['parent_name'] = "%".$params['filter_parent_name']."%";
//        }

        $sql .= " ORDER BY car_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findCarsCount($data)
    {
        $params = $data['params'];

        $conditions = [
            'user_id' => $_SESSION['user_id']
        ];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."user_car WHERE `deleted` = 1  AND `user_Id` = :user_id ";

//        if (!empty($params['filter_parent_name'])) {
//            $sql .= "AND `parent_name` LIKE :parent_name ";
//            $conditions['parent_name'] = "%".$params['filter_parent_name']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addCar($data)
    {
        $param = $data['post'];

        $sql = "SELECT * FROM ".self::$dp."user_car WHERE `plate_number` = :plate_number AND `identification_number` = :identification_number AND `user_id` = 0 ";
        $car_info = self::$db->get_one($sql, ['plate_number'=>$param['plate_number'],'identification_number'=>$param['identification_number']]);

        if (empty($car_info)) {
            $sql = "INSERT INTO ".self::$dp."user_car " .
                " (`user_id`,`plate_number`,`car_type`,`owner`,`address`, " .
                " `use_type`,`brand_type`,`identification_number`,`engine_number`, " .
                " `registration_date`,`accepted_date`,`file_number`,`people_number`, " .
                " `total_mass`,`dimension`,`description`) " .
                " VALUES ";

            $car_id = self::$db->insert(
                $sql,
                [
                    $_SESSION['user_id'],
                    $param['plate_number'],
                    $param['car_type'],
                    $param['owner'],
                    $param['address'],
                    $param['use_type'],
                    $param['brand_type'],
                    $param['identification_number'],
                    $param['engine_number'],
                    $param['registration_date'],
                    $param['accepted_date'],
                    $param['file_number'],
                    $param['people_number'],
                    $param['total_mass'],
                    $param['dimension'],
                    $param['description']
                ]
            );
        } else {
            $update_sql = "UPDATE " . self::$dp . "user_car SET " .
                " `user_id` = :user_id, `plate_number` = :plate_number, `car_type` = :car_type,  " .
                " `owner` = :owner, `address` = :address, `use_type` = :use_type, `brand_type` = :brand_type, " .
                " `identification_number` = :identification_number, `engine_number` = :engine_number,  " .
                " `registration_date` = :registration_date, `accepted_date` = :accepted_date, `description` = :description " .
                " WHERE `car_id` = :car_id";

            self::$db->update(
                $update_sql,
                [
                    'user_id' => $_SESSION['user_id'],
                    'car_id' => $car_info['car_id'],
                    'plate_number' => $param['plate_number'],
                    'car_type' => $param['car_type'],
                    'owner' => $param['owner'],
                    'address' => $param['address'],
                    'use_type' => $param['use_type'],
                    'brand_type' => $param['brand_type'],
                    'identification_number' => $param['identification_number'],
                    'engine_number' => $param['engine_number'],
                    'registration_date' => $param['registration_date'],
                    'accepted_date' => $param['accepted_date'],
                    'description' => $param['description']
                ]
            );

            $sql = "SELECT * FROM ".self::$dp."reservation WHERE `car_id` = :car_id";
            $reservation_info =  self::$db->get_all($sql, ['car_id'=>$car_info['car_id']]);

            if (!empty($reservation_info)) {
                foreach ($reservation_info as $r) {
                    $update_sql1 = "UPDATE " . self::$dp . "reservation SET " .
                        " `user_id` = :user_id " .
                        " WHERE `reservation_id` = :reservation_id AND `user_id` = 0 ";
                    self::$db->update(
                        $update_sql1,
                        [
                            'user_id' => $_SESSION['user_id'],
                            'reservation_id' => $r['reservation_id']
                        ]
                    );
                }
            }

            $car_id = $car_info['car_id'];
        }

        return $car_id;
    }

    public function editCar($data)
    {
        $param = $data['post'];

        $sql = "SELECT car_id FROM ".self::$dp."user_car WHERE `car_id` = :car_id AND `car_id` = :car_id";
        $car_info = self::$db->get_one($sql, ['car_id'=>$param['car_id'], 'user_id'=>$_SESSION['user_id']]);
        if (empty($car_info['car_id']))
            return -1;

        $update_sql = "UPDATE " . self::$dp . "user_car SET " .
            " `plate_number` = :plate_number, `car_type` = :car_type, `owner` = :owner, `address` = :address, " .
            " `use_type` = :use_type, `brand_type` = :brand_type, `identification_number` = :identification_number , `engine_number` = :engine_number, " .
            " `registration_date` = :registration_date, `accepted_date` = :accepted_date, `file_number` = :file_number, `people_number` = :people_number, " .
            " `total_mass` = :total_mass, `dimension` = :dimension, `description` = :description " .
            " WHERE `car_id` = :car_id";

        self::$db->update(
            $update_sql,
            [
                'plate_number' => $param['plate_number'],
                'car_type' => $param['car_type'],
                'owner' => $param['owner'],
                'address' => $param['address'],
                'use_type' => $param['use_type'],
                'brand_type' => $param['brand_type'],
                'identification_number' => $param['identification_number'],
                'engine_number' => $param['engine_number'],
                'registration_date' => $param['registration_date'],
                'accepted_date' => $param['accepted_date'],
                'file_number' => $param['file_number'],
                'people_number' => $param['people_number'],
                'total_mass' => $param['total_mass'],
                'dimension' => $param['dimension'],
                'description' => $param['description'],
                'car_id' => $param['car_id'],
            ]
        );


        $sql = "SELECT * FROM ".self::$dp."user_car WHERE `plate_number` = :plate_number AND `identification_number` = :identification_number AND `user_id` = 0 ";
        $car_info = self::$db->get_one($sql, ['plate_number'=>$param['plate_number'],'identification_number'=>$param['identification_number']]);

        if (!empty($car_info)) {
            $sql = "SELECT * FROM ".self::$dp."reservation WHERE `car_id` = :car_id AND `user_id` = 0 ";
            $reservation_info = self::$db->get_all($sql, ['car_id'=>$car_info['car_id']]);

            if (!empty($reservation_info)) {
                foreach ($reservation_info as $r) {
                    $update_sql1 = "UPDATE " . self::$dp . "reservation SET " .
                        " `user_id` = :user_id, `car_id` = :car_id " .
                        " WHERE `reservation_id` = :reservation_id AND `user_id` = 0 ";
                    self::$db->update(
                        $update_sql1,
                        [
                            'user_id' => $_SESSION['user_id'],
                            'car_id' => $param['car_id'],
                            'reservation_id' => $r['reservation_id']
                        ]
                    );
                }
            }

            $sql = "DELETE FROM ".self::$dp."user_car WHERE `car_id` = :car_id";
            self::$db->delete($sql, ['car_id'=>$car_info['car_id']]);
        }

        return 1;
    }

    public function removeCar($data)
    {
        $sql = "UPDATE ".self::$dp."user_car SET `deleted`=2 WHERE `car_id` = :car_id";

        return self::$db->update($sql, ['car_id'=>$data['car_id']]);
    }

    public function removeCars($data)
    {
        $sql = "UPDATE ".self::$dp."user_car SET `deleted`=2 WHERE `car_id` = :car_id";

        foreach ($data['car_ids'] as $car_id) {
            self::$db->update($sql, ['car_id'=>$car_id]);
        }

        return true;
    }
}
