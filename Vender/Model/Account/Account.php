<?php
namespace Vender\Model\Account;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;

class Account extends DbFactory
{
    public function getInfo($data)
    {
        return self::$db->get_all("SELECT * FROM ".self::$dp."company");
    }

    public function register($data)
    {
        $find_sql = "SELECT * FROM ".self::$dp."company WHERE `tel`=:tel";
        $return = self::$db->get_one($find_sql, ['tel'=>$data['post']['tel']]);
        if (!empty($return) or $return === true)
            return -1;

        $salt = C::get_salt(10);

        $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));

        $find_last_number_sql = "SELECT numbering FROM ".self::$dp."company ORDER BY company_id DESC LIMIT 1";
        $last_return = self::$db->get_one($find_last_number_sql);
        if (empty($last_return)) {
            $numbering = "CPY0000001";
        } else {
            $num = (int)substr($last_return['numbering'], 3) + 1;
            $numbering = "CPY" . sprintf("%07d", $num);
        }

        $sql = "INSERT INTO ".self::$dp."company (`password`,`salt`,`pid`,`tel`,`name`,`numbering`,`type`,`address`,`legal_person`,`registered_capital`,`date_time`,`operating_period`) VALUES ";
        $company_id = self::$db->insert(
            $sql,
            [
                $password,
                $salt,
                0,
                $data['post']['tel'],
                $data['post']['name'],
                $numbering,
                $data['post']['type'],
                $data['post']['address'],
                $data['post']['legal_person'],
                $data['post']['registered_capital'],
                $data['post']['date_time'],
                $data['post']['operating_period']
            ]
        );

        return $company_id;
    }

    public function login($data)
    {
        $sql = "SELECT * FROM ".self::$dp."company WHERE `tel`=:tel AND `password`=SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1(" . self::$db->quote($data['password']) . "))))) AND deleted = 1";
        #$sql = "SELECT * FROM ".self::$dp."admin WHERE `username`=:username AND `password`=:password";

        $company_info = self::$db->get_one(
            $sql,
            [
                'tel'=>$data['tel'],
                #'password'=>"SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('{$data['password']}')))))"
            ]
        );

        $date = date('Y-m-d H:i:s', time());
        if (!empty($company_info)) {
            $update_sql = "UPDATE ".self::$dp."company SET `last_login_time`='{$date}' WHERE `tel`=:tel AND `password`=SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1(" . self::$db->quote($data['password']) . ")))))";
            self::$db->update($update_sql, ['tel'=>$data['tel']]);
        }

        return $company_info;
    }
}
