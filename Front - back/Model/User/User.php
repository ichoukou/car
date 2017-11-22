<?php
namespace Front\Model\User;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;

class User extends DbFactory
{
    public function findUserBySession($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user WHERE `user_id` = :user_id";

        return self::$db->get_one($sql, ['user_id'=>$_SESSION['user_id']]);
    }

    public function editUser($data)
    {
        $conditions = [
            'user_id' => $_SESSION['user_id']
        ];
        $conditions_sql = '';
        if (!empty($data['post']['password'])) {
            $salt = C::get_salt(10);
            $conditions_sql .= " `salt` = :salt ";
            $conditions['salt'] = $salt;

            $conditions_sql .= " ,`password` = :password ";
            $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));
            $conditions['password'] = $password;

            $update_sql = " UPDATE " . self::$dp . "user SET {$conditions_sql} WHERE `user_id` = :user_id";

            self::$db->update($update_sql, $conditions);
        }
    }
}
