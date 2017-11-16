<?php
namespace Front\Model\Account;

use Libs\Core\DbFactory AS DbFactory;

class Account extends DbFactory
{
    public function getInfo($data)
    {
        return self::$db->get_all("SELECT * FROM ".self::$dp."user");
    }

    public function register($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user WHERE `username`=:username";

        $return = self::$db->get_one($sql, ['username'=>$data['username']]);

        if (!empty($return) or $return === true) return -1;

        $sql = "INSERT INTO ".self::$dp."user (`username`,`password`,`salt`) VALUES";

        $salt = $this->token(10);

        $new_password = sha1($salt . sha1($salt . sha1($data['password'])));

        return self::$db->insert($sql, [$data['username'], $new_password, $salt]);
    }

    public function login($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user WHERE `username`=:username AND `password`=SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1(" . self::$db->quoto($data['password']) . ")))))";
        #$sql = "SELECT * FROM ".self::$dp."user WHERE `username`=:username AND `password`=:password";

        return self::$db->get_one($sql,
            [
                'username'=>$data['username'],
                #'password'=>"SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('{$data['password']}')))))"
            ]
        );
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
