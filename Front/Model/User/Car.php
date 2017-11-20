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
        $sql = "INSERT INTO ".self::$dp."user_car " .
            " (`user_id`,`plate_number`,`car_type`,`owner`,`address`, " .
            " `use_type`,`brand_type`,`identification_number`,`engine_number`, " .
            " `registration_date`,`accepted_date`,`file_number`,`people_number`, " .
            " `total_mass`,`dimension`,`description`) " .
            " VALUES ";

        return self::$db->insert(
            $sql,
            [
                $_SESSION['user_id'],
                $data['post']['plate_number'],
                $data['post']['car_type'],
                $data['post']['owner'],
                $data['post']['address'],
                $data['post']['use_type'],
                $data['post']['brand_type'],
                $data['post']['identification_number'],
                $data['post']['engine_number'],
                $data['post']['registration_date'],
                $data['post']['accepted_date'],
                $data['post']['file_number'],
                $data['post']['people_number'],
                $data['post']['total_mass'],
                $data['post']['dimension'],
                $data['post']['description']
            ]
        );
    }

    public function editCar($data)
    {
        $sql = "SELECT car_id FROM ".self::$dp."user_car WHERE `car_id` = :car_id AND `car_id` = :car_id";
        $car_info = self::$db->get_one($sql, ['car_id'=>$data['post']['car_id'], 'user_id'=>$_SESSION['user_id']]);
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
                'plate_number' => $data['post']['plate_number'],
                'car_type' => $data['post']['car_type'],
                'owner' => $data['post']['owner'],
                'address' => $data['post']['address'],
                'use_type' => $data['post']['use_type'],
                'brand_type' => $data['post']['brand_type'],
                'identification_number' => $data['post']['identification_number'],
                'engine_number' => $data['post']['engine_number'],
                'registration_date' => $data['post']['registration_date'],
                'accepted_date' => $data['post']['accepted_date'],
                'file_number' => $data['post']['file_number'],
                'people_number' => $data['post']['people_number'],
                'total_mass' => $data['post']['total_mass'],
                'dimension' => $data['post']['dimension'],
                'description' => $data['post']['description'],
                'car_id' => $data['post']['car_id'],
            ]
        );

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
