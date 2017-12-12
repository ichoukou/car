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
        $sql = "INSERT INTO ".self::$dp."user_car " .
            " (`plate_number`,`car_type`,`owner`,`address`, " .
            " `use_type`,`brand_type`,`identification_number`,`engine_number`, " .
            " `registration_date`,`accepted_date`,`file_number`,`people_number`, " .
            " `total_mass`,`dimension`,`description`) " .
            " VALUES ";

        $car_id = self::$db->insert(
            $sql,
            [
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

        $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

        $bill = date('YmdHis', time()).$orderSn.$_SESSION['user_id'];

        $sql = "INSERT INTO ".self::$dp."reservation (`company_id`,`car_id`,`bill`,`reservation_time`,`description`) VALUES ";
        $reservation_id = self::$db->insert(
            $sql,
            [
                $_SESSION['company_id'],
                $car_id,
                $bill,
                date('Y-m-d H:i', time()),
                ''
            ]
        );

        return $reservation_id;
    }
}
