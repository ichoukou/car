<?php
namespace Vender\Model\ReceivingCar;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;

class ReceivingCar extends DbFactory
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

    public function receiving_car($data)
    {
//        $find_sql = "SELECT * FROM ".self::$dp."user WHERE `tel`=:tel";
//        $return = self::$db->get_one($find_sql, ['tel'=>$data['post']['tel']]);
//        if (!empty($return) or $return === true)
//            return -1;
//
//        $salt = C::get_salt(10);
//
//        $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));
//
//        $find_last_number_sql = "SELECT numbering FROM ".self::$dp."user ORDER BY user_id DESC LIMIT 1";
//        $last_return = self::$db->get_one($find_last_number_sql);
//        if (empty($last_return)) {
//            $numbering = "USER0000001";
//        } else {
//            $num = (int)substr($last_return['numbering'], 4) + 1;
//            $numbering = "USER" . sprintf("%07d", $num);
//        }

        $sql = "INSERT INTO ".self::$dp."user (`tel`) VALUES ";
        $user_id = self::$db->insert(
            $sql,
            [
                $data['post']['tel']
            ]
        );

        $sql = "INSERT INTO ".self::$dp."user_car " .
            " (`user_id`,`plate_number`,`car_type`,`owner`,`address`, " .
            " `use_type`,`brand_type`,`identification_number`,`engine_number`, " .
            " `registration_date`,`accepted_date`,`file_number`,`people_number`, " .
            " `total_mass`,`dimension`,`description`) " .
            " VALUES ";
        $car_id = self::$db->insert(
            $sql,
            [
                $user_id,
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

        $find_sql = "SELECT u.user_id,u.tel,uc.car_id FROM ".self::$dp."user AS u LEFT JOIN ".self::$dp."user_car AS uc ON u.user_id = uc.user_id WHERE u.`user_id` = :user_id";
        $user_info = self::$db->get_one($find_sql, ['user_id'=>$user_id]);

        $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

        $bill = date('YmdHis', time()).$orderSn.$_SESSION['user_id'];

        $sql = "INSERT INTO ".self::$dp."reservation (`company_id`,`user_id`,`car_id`,`bill`,`reservation_time`,`description`) VALUES ";
        $reservation_id = self::$db->insert(
            $sql,
            [
                $_SESSION['company_id'],
                $user_info['user_id'],
                $user_info['car_id'],
                $bill,
                date('Y-m-d H:i', time()),
                ''
            ]
        );

        return $reservation_id;
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
