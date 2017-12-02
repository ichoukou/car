<?php
namespace Front\Model\Inquire;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\WxCommon as WC;

class Inquire extends DbFactory
{
    public function findCarByUserId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user_car WHERE `user_id` = :user_id";

        return self::$db->get_one($sql, ['user_id'=>$_SESSION['user_id']]);
    }
}
