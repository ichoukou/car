<?php
namespace Admin\Model\User;

use Libs\Core\DbFactory AS DbFactory;

class User extends DbFactory
{
    public function findUserByEmail($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user WHERE `email` = :email";

        return self::$db->get_one($sql, ['email'=>$data['email']]);
    }

    public function findUserByTel($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user WHERE `tel` = :tel";

        return self::$db->get_one($sql, ['tel'=>$data['tel']]);
    }

    public function findUserByUserId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user WHERE `user_id` = :user_id";

        return self::$db->get_one($sql, ['user_id'=>$data['user_id']]);
    }

    public function findUsers($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT * FROM ".self::$dp."user WHERE `deleted` = 1 ";

        if (!empty($params['filter_username'])) {
            $sql .= "AND `username` LIKE :username ";
            $conditions['username'] = "%".$params['filter_username']."%";
        }

        if (!empty($params['filter_parent_name'])) {
            $sql .= "AND `parent_name` LIKE :parent_name ";
            $conditions['parent_name'] = "%".$params['filter_parent_name']."%";
        }

        if (!empty($params['filter_tel'])) {
            $sql .= "AND `tel` LIKE :tel ";
            $conditions['tel'] = "%".$params['filter_tel']."%";
        }

        if (!empty($params['filter_job'])) {
            $sql .= "AND `job` LIKE :job ";
            $conditions['job'] = "%".$params['filter_job']."%";
        }

        if (!empty($params['filter_user_group_id'])) {
            $sql .= "AND `user_group_id` LIKE :user_group_id ";
            $conditions['user_group_id'] = $params['filter_user_group_id'];
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY user_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findUsersCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."user WHERE `deleted` = 1 ";

        if (!empty($params['filter_username'])) {
            $sql .= "AND `username` LIKE :username ";
            $conditions['username'] = "%".$params['filter_username']."%";
        }

        if (!empty($params['filter_parent_name'])) {
            $sql .= "AND `parent_name` LIKE :parent_name ";
            $conditions['parent_name'] = "%".$params['filter_parent_name']."%";
        }

        if (!empty($params['filter_tel'])) {
            $sql .= "AND `tel` LIKE :tel ";
            $conditions['tel'] = "%".$params['filter_tel']."%";
        }

        if (!empty($params['filter_job'])) {
            $sql .= "AND `job` LIKE :job ";
            $conditions['job'] = "%".$params['filter_job']."%";
        }

        if (!empty($params['filter_user_group_id'])) {
            $sql .= "AND `user_group_id` LIKE :user_group_id ";
            $conditions['user_group_id'] = $params['filter_user_group_id'];
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addUser($data)
    {
        $sql = "INSERT INTO ".self::$dp."user (`user_group_id`,`parent_name`,`tel`,`job`,`email`,`newsletter`) VALUES ";

        return self::$db->insert(
            $sql,
            [
                $data['post']['user_group_id'],
                $data['post']['parent_name'],
                $data['post']['tel'],
                $data['post']['job'],
                $data['post']['email'],
                5
            ]
        );
    }

    public function editUser($data)
    {
        $update_sql = "UPDATE " . self::$dp . "user SET"
            ." `user_group_id` = :user_group_id, `email` = :email, "
            ." `parent_name` = :parent_name, `tel` = :tel, `job` = :job "
            #." `newsletter` = :newsletter "
            ." WHERE `user_id` = :user_id";

        self::$db->update(
            $update_sql,
            [
                'user_group_id'     => $data['post']['user_group_id'],
                'parent_name'       => $data['post']['parent_name'],
                'tel'               => $data['post']['tel'],
                'job'               => $data['post']['job'],
                'email'             => $data['post']['email'],
                #'newsletter'        => $data['post']['newsletter'],
                'user_id'           => $data['post']['user_id'],
            ]
        );
    }

    public function removeUser($data)
    {
        $sql = "UPDATE ".self::$dp."user SET `deleted`=2 WHERE `user_id` = :user_id";

        return self::$db->update($sql, ['user_id'=>$data['user_id']]);
    }

    public function removeUsers($data)
    {
        $sql = "UPDATE ".self::$dp."user SET `deleted`=2 WHERE `user_id` = :user_id";

        foreach ($data['user_ids'] as $user_id) {
            self::$db->update($sql, ['user_id'=>$user_id]);
        }

        return true;
    }
}
