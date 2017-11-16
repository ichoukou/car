<?php
namespace Admin\Model\System;

use Libs\Core\DbFactory AS DbFactory;

class Adm extends DbFactory
{
    public function findAdmByAdminId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."admin " .
            " WHERE `deleted` = 1 AND `admin_id` = :admin_id";

        $result = self::$db->get_one($sql, ['admin_id'=>$data['admin_id']]);

        return $result;
    }

    public function findAdms($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT a.* FROM ".self::$dp."admin AS a " .
            #" LEFT JOIN ".self::$dp."setting AS s ON t.setting_id = s.setting_id " .
            " WHERE a.`deleted` = 1 AND a.`group` >= '{$_SESSION['group']}' ";


        $sql .= " ORDER BY a.admin_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findAdmsCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."admin AS a " .
            #" LEFT JOIN ".self::$dp."setting AS s ON t.setting_id = s.setting_id " .
            " WHERE a.`deleted` = 1 AND a.`group` >= '{$_SESSION['group']}' ";

        return self::$db->count($sql, $conditions);
    }

    public function addAdm($data)
    {
        $find_sql = "SELECT * FROM ".self::$dp."admin WHERE `username`=:username";
        $return = self::$db->get_one($find_sql, ['username'=>$data['post']['username']]);

        if (!empty($return) or $return === true)
            return -1;

        $salt = $this->token(10);

        $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));

        $sql = "INSERT INTO ".self::$dp."admin (`group`,`username`,`password`,`tel`,`email`,`salt`) VALUES ";
        $admin_id = self::$db->insert(
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

        return $admin_id;
    }

    public function editAdm($data)
    {
        $conditions = [
            'admin_id'           => $data['post']['admin_id'],
            'tel'                 => $data['post']['tel'],
            'email'              => $data['post']['email']
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


        $update_sql = " UPDATE " . self::$dp . "admin SET `tel` = :tel, email = :email {$conditions_sql} WHERE `admin_id` = :admin_id";

        self::$db->update($update_sql, $conditions);
    }

    public function removeAdm($data)
    {
        $remove_admin_info = $this->findAdmByAdminId($data);

        if ($remove_admin_info['group'] < $_SESSION['group'])
            return false;

        $sql = "UPDATE ".self::$dp."admin SET `deleted`=2 WHERE `admin_id` = :admin_id";

        return self::$db->update($sql, ['admin_id'=>$data['admin_id']]);
    }

    public function removeAdms($data)
    {
        $sql = "UPDATE ".self::$dp."admin SET `deleted`=2 WHERE `admin_id` = :admin_id";

        foreach ($data['admin_ids'] as $admin_id) {
            $remove_admin_info = $this->findAdmByAdminId(['admin_id'=>$admin_id]);

            if ($remove_admin_info['group'] < $_SESSION['group'])
                continue;

            self::$db->update($sql, ['admin_id'=>$admin_id]);
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
