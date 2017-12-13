<?php
namespace Front\Model\Account;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;

class Account extends DbFactory
{
    public function findAccountByTel($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user WHERE `tel` = :tel";

        return self::$db->get_one($sql, ['tel'=>$data['tel']]);
    }

    public function getInfo($data)
    {
        return self::$db->get_all("SELECT * FROM ".self::$dp."user");
    }

//    public function register($data)
//    {
//        $find_sql = "SELECT * FROM ".self::$dp."user WHERE `tel`=:tel";
//        $return = self::$db->get_one($find_sql, ['tel'=>$data['post']['tel']]);
//        if (!empty($return) or $return === true)
//            return -1;
//
//        $salt = C::get_salt(10);
//
//        $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));
//
//        $find_last_number_sql = "SELECT numbering FROM ".self::$dp."user WHERE `numbering` is not null ORDER BY user_id DESC LIMIT 1";
//        $last_return = self::$db->get_one($find_last_number_sql);
//        if (empty($last_return)) {
//            $numbering = "USER0000001";
//        } else {
//            $num = (int)substr($last_return['numbering'], 4) + 1;
//            $numbering = "USER" . sprintf("%07d", $num);
//        }
//
//        $sql = "INSERT INTO ".self::$dp."user (`password`,`salt`,`tel`,`numbering`) VALUES ";
//        $user_id = self::$db->insert(
//            $sql,
//            [
//                $password,
//                $salt,
//                $data['post']['tel'],
//                $numbering
//            ]
//        );
//
//        $sql = "INSERT INTO ".self::$dp."user_car " .
//            " (`user_id`,`plate_number`,`car_type`,`owner`,`address`, " .
//            " `use_type`,`brand_type`,`identification_number`,`engine_number`, " .
//            " `registration_date`,`accepted_date`,`file_number`,`people_number`, " .
//            " `total_mass`,`dimension`,`description`) " .
//            " VALUES ";
//        $car_id = self::$db->insert(
//            $sql,
//            [
//                $user_id,
//                $data['post']['plate_number'],
//                $data['post']['car_type'],
//                $data['post']['owner'],
//                $data['post']['address'],
//                $data['post']['use_type'],
//                $data['post']['brand_type'],
//                $data['post']['identification_number'],
//                $data['post']['engine_number'],
//                $data['post']['registration_date'],
//                $data['post']['accepted_date'],
//                $data['post']['file_number'],
//                $data['post']['people_number'],
//                $data['post']['total_mass'],
//                $data['post']['dimension'],
//                $data['post']['description']
//            ]
//        );
//
//        return $user_id;
//    }

    public function register($data)
    {
        $param = $data['post'];

        $find_sql = "SELECT * FROM ".self::$dp."user WHERE `tel`=:tel";
        $return = self::$db->get_one($find_sql, ['tel'=>$param['tel']]);
        if (!empty($return) or $return === true)
            return -1;

        $salt = C::get_salt(10);

        $password = sha1($salt . sha1($salt . sha1($param['password'])));

        $find_last_number_sql = "SELECT numbering FROM ".self::$dp."user WHERE `numbering` is not null ORDER BY user_id DESC LIMIT 1";
        $last_return = self::$db->get_one($find_last_number_sql);
        if (empty($last_return)) {
            $numbering = "USER0000001";
        } else {
            $num = (int)substr($last_return['numbering'], 4) + 1;
            $numbering = "USER" . sprintf("%07d", $num);
        }

        $sql = "INSERT INTO ".self::$dp."user (`password`,`salt`,`tel`,`numbering`) VALUES ";
        $user_id = self::$db->insert(
            $sql,
            [
                $password,
                $salt,
                $param['tel'],
                $numbering
            ]
        );

        if (empty($param['plate_number']) and empty($param['identification_number'])) {
            return $user_id;
        } elseif (!empty($param['plate_number']) and !empty($param['identification_number'])) {
            $sql = "SELECT * FROM ".self::$dp."user_car WHERE `plate_number` = :plate_number AND `identification_number` = :identification_number AND `user_id` = 0 ";
            $car_info = self::$db->get_one($sql, ['plate_number'=>$param['plate_number'],'identification_number'=>$param['identification_number']]);
        } elseif(!empty($param['plate_number'])) {
            $sql = "SELECT * FROM ".self::$dp."user_car WHERE `plate_number` = :plate_number AND `user_id` = 0 ";
            $car_info = self::$db->get_one($sql, ['plate_number'=>$param['plate_number']]);
        } elseif(!empty($param['identification_number'])) {
            $sql = "SELECT * FROM ".self::$dp."user_car WHERE `identification_number` = :identification_number AND `user_id` = 0 ";
            $car_info = self::$db->get_one($sql, ['identification_number'=>$param['identification_number']]);
        }

        if (empty($car_info)) {
            $sql = "INSERT INTO ".self::$dp."user_car " .
                " (`user_id`,`plate_number`,`identification_number`) " .
                " VALUES ";

            $car_id = self::$db->insert(
                $sql,
                [
                    $user_id,
                    $param['plate_number'],
                    $param['identification_number']
                ]
            );
        } else {
            $update_sql = "UPDATE " . self::$dp . "user_car SET " .
                          " `user_id` = :user_id " .
                          " WHERE `car_id` = :car_id";
            self::$db->update(
                $update_sql,
                [
                    'user_id' => $user_id,
                    'car_id' => $car_info['car_id']
                ]
            );

            $sql = "SELECT * FROM ".self::$dp."reservation WHERE `car_id` = :car_id";
            $reservation_info =  self::$db->get_all($sql, ['car_id'=>$car_info['car_id']]);

            if (!empty($reservation_info)) {
                foreach ($reservation_info as $r) {
                    $update_sql1 = "UPDATE " . self::$dp . "reservation SET " .
                        " `user_id` = :user_id " .
                        " WHERE `car_id` = :car_id AND `user_id` = 0 ";
                    self::$db->update(
                        $update_sql1,
                        [
                            'user_id' => $user_id,
                            'car_id' => $car_info['car_id']
                        ]
                    );
                }
            }
        }

        return $user_id;
    }

    public function login($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user WHERE `tel`=:tel AND `password`=SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1(" . self::$db->quote($data['password']) . "))))) AND deleted = 1";
        #$sql = "SELECT * FROM ".self::$dp."admin WHERE `username`=:username AND `password`=:password";

        $user_info = self::$db->get_one(
            $sql,
            [
                'tel'=>$data['tel'],
                #'password'=>"SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('{$data['password']}')))))"
            ]
        );

        $date = date('Y-m-d H:i:s', time());
        if (!empty($user_info)) {
            $update_sql = "UPDATE ".self::$dp."user SET `last_login_time`='{$date}' WHERE `tel`=:tel AND `password`=SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1(" . self::$db->quote($data['password']) . ")))))";
            self::$db->update($update_sql, ['tel'=>$data['tel']]);
        }

        return $user_info;
    }

    public function getSmsInfo($data)
    {
        $sql = "SELECT * FROM ".self::$dp."sms WHERE `obj_type` = 2 AND `sms_type` = 1 AND `tel` = :tel";
        return self::$db->get_one(
            $sql,
            [
                'tel' => $data['tel']
            ]
        );
    }

    public function addSms($data)
    {
        $sql = "INSERT INTO ".self::$dp."sms (`tel`,`rand_number`,`sms_type`,`obj_type`,`send_time`) VALUES ";

        return self::$db->insert(
            $sql,
            [
                $data['tel'],
                $data['rand_number'],
                1,
                2,
                time()
            ]
        );
    }

    public function editSms($data)
    {
        $update_sql = "UPDATE " . self::$dp . "sms SET `return_time` = :return_time, `return_type` = :return_type WHERE `sms_id` = :sms_id ";

        self::$db->update(
            $update_sql,
            [
                'sms_id'        => $data['sms_id'],
                'return_time'   => time(),
                'return_type'   => $data['return_type']
            ]
        );
    }

    public function validateSms($data)
    {
        $sql = "SELECT * FROM ".self::$dp."sms WHERE `obj_type` = 2 AND `sms_type` = 1 AND `tel` = :tel AND `rand_number` = :code ";
        return self::$db->get_one(
            $sql,
            [
                'tel'   => $data['tel'],
                'code'  => $data['code']
            ]
        );
    }

    public function delSms($data)
    {
        $update_sql = "DELETE FROM " . self::$dp . "sms WHERE `tel` = :tel AND `obj_type` = 2 AND `sms_type` = 1";

        self::$db->update(
            $update_sql,
            [
                'tel'        => $data['tel']
            ]
        );
    }
}
