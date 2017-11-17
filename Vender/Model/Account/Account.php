<?php
namespace Vender\Model\Account;

use Libs\Core\DbFactory AS DbFactory;

class Account extends DbFactory
{
    public function getInfo($data)
    {
        return self::$db->get_all("SELECT * FROM ".self::$dp."vender");
    }

    public function register($data)
    {
        $sql = "SELECT * FROM ".self::$dp."vender WHERE `username`=:username";

        $return = self::$db->get_one($sql, ['username'=>$data['username']]);

        if (!empty($return) or $return === true) return -1;

        $sql = "INSERT INTO ".self::$dp."vender (`username`,`password`,`salt`) VALUES";

        $salt = $this->token(10);

        $new_password = sha1($salt . sha1($salt . sha1($data['password'])));

        return self::$db->insert($sql, [$data['username'], $new_password, $salt]);
    }

    public function login($data)
    {
        $sql = "SELECT * FROM ".self::$dp."vender WHERE `username`=:username AND `password`=SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1(" . self::$db->quote($data['password']) . ")))))";
        #$sql = "SELECT * FROM ".self::$dp."admin WHERE `username`=:username AND `password`=:password";

        $vender_info = self::$db->get_one(
            $sql,
            [
                'username'=>$data['username'],
                #'password'=>"SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('{$data['password']}')))))"
            ]
        );

        $date = date('Y-m-d H:i:s', time());
        if (!empty($vender_info)) {
            $update_sql = "UPDATE ".self::$dp."vender SET `last_login_time`='{$date}' WHERE `username`=:username AND `password`=SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1(" . self::$db->quote($data['password']) . ")))))";
            self::$db->update($update_sql, ['username'=>$data['username']]);
        }

        return $vender_info;
    }

    public function token($length = 20)
    {
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        $max = strlen($string) - 1;

        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $string[mt_rand(0, $max)];
        }

        return $token;
    }
}
