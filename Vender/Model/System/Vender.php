<?php
namespace Vender\Model\System;

use Libs\Core\DbFactory AS DbFactory;

class Vender extends DbFactory
{
    public function findVenderByVenderId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."vender " .
            " WHERE `deleted` = 1 AND `vender_id` = :vender_id";

        $result = self::$db->get_one($sql, ['vender_id'=>$data['vender_id']]);

        return $result;
    }

    public function findVenders($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT a.* FROM ".self::$dp."vender AS a " .
            #" LEFT JOIN ".self::$dp."setting AS s ON t.setting_id = s.setting_id " .
            " WHERE a.`deleted` = 1 AND a.`group` >= '{$_SESSION['group']}' ";


        $sql .= " ORDER BY a.vender_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findVendersCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."vender AS a " .
            #" LEFT JOIN ".self::$dp."setting AS s ON t.setting_id = s.setting_id " .
            " WHERE a.`deleted` = 1 AND a.`group` >= '{$_SESSION['group']}' ";

        return self::$db->count($sql, $conditions);
    }

    public function addVender($data)
    {
        $find_sql = "SELECT * FROM ".self::$dp."vender WHERE `username`=:username";
        $return = self::$db->get_one($find_sql, ['username'=>$data['post']['username']]);

        if (!empty($return) or $return === true)
            return -1;

        $salt = $this->token(10);

        $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));

        $sql = "INSERT INTO ".self::$dp."vender (`group`,`username`,`password`,`tel`,`email`,`salt`) VALUES ";
        $vender_id = self::$db->insert(
            $sql,
            [
                $data['post']['group'],
                $data['post']['username'],
                $password,
                $data['post']['tel'],
                $data['post']['email'],
                $salt
            ]
        );

        return $vender_id;
    }

    public function editVender($data)
    {
        $conditions = [
            'vender_id'   => $data['post']['vender_id'],
            'tel'         => $data['post']['tel'],
            'email'       => $data['post']['email']
        ];

        $conditions_sql = '';

        if (!empty($data['post']['group'])) {
            $conditions_sql .= " ,`group` = :group ";
            $conditions['group'] = $data['post']['group'];
        }

        if (!empty($data['post']['password'])) {
            $salt = $this->token(10);
            $conditions_sql .= " ,`salt` = :salt ";
            $conditions['salt'] = $salt;

            $conditions_sql .= " ,`password` = :password ";
            $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));
            $conditions['password'] = $password;
        }

        $update_sql = " UPDATE " . self::$dp . "vender SET `tel` = :tel, email = :email {$conditions_sql} WHERE `vender_id` = :vender_id";

        self::$db->update($update_sql, $conditions);
    }

    public function removeVender($data)
    {
        $remove_vender_info = $this->findVenderByVenderId($data);

        if ($remove_vender_info['group'] < $_SESSION['group'])
            return false;

        $sql = "UPDATE ".self::$dp."vender SET `deleted`=2 WHERE `vender_id` = :vender_id";

        return self::$db->update($sql, ['vender_id'=>$data['vender_id']]);
    }

    public function removeVenders($data)
    {
        $sql = "UPDATE ".self::$dp."vender SET `deleted`=2 WHERE `vender_id` = :vender_id";

        foreach ($data['vender_ids'] as $vender_id) {
            $remove_vender_info = $this->findVenderByVenderId(['vender_id'=>$vender_id]);

            if ($remove_vender_info['group'] < $_SESSION['group'])
                continue;

            self::$db->update($sql, ['vender_id'=>$vender_id]);
        }

        return true;
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
