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
        $param = $data['post'];

        if (!empty($param['plate_number']) and !empty($param['identification_number'])) {
            $sql = "SELECT * FROM ".self::$dp."user_car WHERE `plate_number` = :plate_number AND `identification_number` = :identification_number AND `user_id` != 0 ";
            $car_info = self::$db->get_one($sql, ['plate_number'=>$param['plate_number'],'identification_number'=>$param['identification_number']]);
        } elseif(!empty($param['plate_number'])) {
            $sql = "SELECT * FROM ".self::$dp."user_car WHERE `plate_number` = :plate_number AND `user_id` != 0 ";
            $car_info = self::$db->get_one($sql, ['plate_number'=>$param['plate_number']]);
        } elseif(!empty($param['identification_number'])) {
            $sql = "SELECT * FROM ".self::$dp."user_car WHERE `identification_number` = :identification_number AND `user_id` != 0 ";
            $car_info = self::$db->get_one($sql, ['identification_number'=>$param['identification_number']]);
        }

        if (empty($car_info)) {
            $sql = "INSERT INTO ".self::$dp."user_car " .
                " (`plate_number`,`identification_number`) " .
                " VALUES ";

            $car_id = self::$db->insert(
                $sql,
                [
                    $param['plate_number'],
                    $param['identification_number']
                ]
            );

            $user_id = 0;
        } else {
            $car_id = $car_info['car_id'];
            $user_id = $car_info['user_id'];
        }

        $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

        $bill = date('YmdHis', time()).$orderSn.$_SESSION['user_id'];

        $sql = "INSERT INTO ".self::$dp."reservation (`company_id`,`user_id`,`car_id`,`bill`,`reservation_time`,`description`) VALUES ";
        $reservation_id = self::$db->insert(
            $sql,
            [
                $_SESSION['company_id'],
                $user_id,
                $car_id,
                $bill,
                date('Y-m-d H:i', time()),
                ''
            ]
        );

        return $reservation_id;
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
