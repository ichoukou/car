<?php
namespace Admin\Model\User;

use Libs\Core\DbFactory AS DbFactory;

class Baby extends DbFactory
{
    public function autocompleteFindBabyCharacter($data)
    {
        $result = '';
        $conditions = [];

        if (!empty($data['post']['module'])) {
            $category_conditions = ['module'=>$data['post']['module']];
            if (!empty($data['post']['key'])) {
                $key = " AND `key` = :key ";
                $category_conditions['key'] = $data['post']['key'];
            }

            $sql = "SELECT * FROM ".self::$dp."setting_category WHERE `module` = :module {$key} AND `deleted` = 1";

            $setting_category = self::$db->get_one($sql, $category_conditions);

            if (!empty($setting_category)) {
                $setting_sql = "SELECT `setting_id`,value FROM ".self::$dp."setting WHERE `setting_category_id` = :setting_category_id AND `deleted` = 1";

                $conditions['setting_category_id'] = $setting_category['setting_category_id'];

                if (!empty($data['post']['filter_param'])) {
                    $setting_sql .= " AND `value` LIKE :value ";
                    $conditions['value'] = "%".$data['post']['filter_param']."%";
                }

                $result = self::$db->get_all($setting_sql, $conditions);
            }
        }

        return $result;
    }

    public function findBabyByBabyId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."user_baby WHERE `baby_id` = :baby_id AND deleted = 1";
        $baby = self::$db->get_one($sql, ['baby_id'=>$data['baby_id']]);

        $character_sql = "SELECT ubc.*,s.value FROM ".self::$dp."user_baby_character AS ubc LEFT JOIN ".self::$dp."setting AS s ON ubc.setting_id=s.setting_id WHERE ubc.`baby_id` = :baby_id ";
        $baby['characters'] = self::$db->get_all($character_sql, ['baby_id'=>$data['baby_id']]);

        return $baby;
    }

    public function findBabys($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT b.*,u.parent_name,u.tel FROM ".self::$dp."user_baby AS b".
               " LEFT JOIN ".self::$dp."user AS u ON b.user_id=u.user_id ".
               " WHERE b.`deleted` = 1 ";

        if (!empty($params['user_id'])) {
            $sql .= "AND b.`user_id` = :user_id ";
            $conditions['user_id'] = $params['user_id'];
        }

        if (!empty($params['filter_name'])) {
            $sql .= "AND b.`name` LIKE :name ";
            $conditions['name'] = "%".$params['filter_name']."%";
        }

        if (!empty($params['filter_age'])) {
            $sql .= "AND b.`age` = :age ";
            $conditions['age'] = $params['filter_age'];
        }

        if (!empty($params['filter_sex'])) {
            $sql .= "AND b.`sex` = :sex ";
            $conditions['sex'] = $params['filter_sex'];
        }

        if (!empty($params['filter_birthday'])) {
            $sql .= "AND b.`birthday` = :birthday ";
            $conditions['birthday'] = $params['filter_birthday'];
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND b.`create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY b.baby_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findBabysCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."user_baby AS b WHERE b.`deleted` = 1 ";

        if (!empty($params['user_id'])) {
            $sql .= "AND b.`user_id` = :user_id ";
            $conditions['user_id'] = $params['user_id'];
        }

        if (!empty($params['filter_name'])) {
            $sql .= "AND b.`name` LIKE :name ";
            $conditions['name'] = "%".$params['filter_name']."%";
        }

        if (!empty($params['filter_age'])) {
            $sql .= "AND b.`age` = :age ";
            $conditions['age'] = $params['filter_age'];
        }

        if (!empty($params['filter_sex'])) {
            $sql .= "AND b.`sex` = :sex ";
            $conditions['sex'] = $params['filter_sex'];
        }

        if (!empty($params['filter_birthday'])) {
            $sql .= "AND b.`birthday` = :birthday ";
            $conditions['birthday'] = $params['filter_birthday'];
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND b.`create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addBaby($data)
    {
        $data['post']['age'] = (int)$data['post']['age'];
        $data['post']['body_height'] = (float)$data['post']['body_height'];
        $data['post']['body_weight'] = (float)$data['post']['body_weight'];

        $fileds =
            [
                'user_id','name','age','sex','body_height','body_weight', 'birthday',
                'zodiac','constellation','blood_type','family_situation',
                'breakfast','lunch','dinner','sleeping_time'
            ];

        $splice_sql = '';
        $param = [];
        foreach ($data['post'] as $filed=>$value) {
            if (in_array($filed, $fileds) and !empty($value)) {
                if (!empty($splice_sql)) $splice_sql .= ',';
                $splice_sql .= " `{$filed}` ";
                array_push($param, $value);
            }
        }

        $sql = "INSERT INTO ".self::$dp."user_baby ({$splice_sql}) VALUES ";
        self::$db->insert($sql, $param);

        $find_last_baby_sql = "SELECT baby_id FROM ".self::$dp."user_baby WHERE `user_id` = :user_id ORDER BY baby_id DESC ";
        $baby_info = self::$db->get_one($find_last_baby_sql, ['user_id'=>$data['post']['user_id']]);

        /**
         * 添加宝宝性格
         */
        if (count($data['post']['baby_character_setting_id']) > 0) {
            $insert_baby_character_sql = "INSERT INTO ".self::$dp."user_baby_character (`baby_id`,`setting_id`) VALUES ";
            $baby_character_options = [];
            foreach ($data['post']['baby_character_setting_id'] as $baby_character_key=>$baby_character_setting_id) {
                $baby_character_options[$baby_character_key][0] = $baby_info['baby_id'];
                $baby_character_options[$baby_character_key][1] = $baby_character_setting_id;
            }
            self::$db->insert($insert_baby_character_sql, $baby_character_options);
        }

        return $baby_info['baby_id'];
    }

    public function editBaby($data)
    {
        $data['post']['age'] = (int)$data['post']['age'];
        $data['post']['body_height'] = (float)$data['post']['body_height'];
        $data['post']['body_weight'] = (float)$data['post']['body_weight'];

        $fileds =
            [
                'name','age','sex','body_height','body_weight', 'birthday',
                'zodiac','constellation','blood_type','family_situation', 'baby_character',
                'breakfast','lunch','dinner','sleeping_time'
            ];

        $splice_sql = '';
        $param = [];
        foreach ($data['post'] as $filed=>$value) {
            if (in_array($filed, $fileds) and !empty($value)) {
                if (!empty($splice_sql)) $splice_sql .= ',';
                $splice_sql .= " `{$filed}` = :{$filed} ";
                $param[$filed] = $value;
            }
        }

        $param['baby_id'] = $data['post']['baby_id'];

        $update_sql = "UPDATE " . self::$dp . "user_baby SET {$splice_sql} WHERE `baby_id` = :baby_id";

        #self::$db->update($update_sql, $param);

        /**
         * $new_settings 表单提交的教具选项数组
         * $need_remove_setting_id 需要从数据库删除的教具选项数组
         * $need_add_setting_id 需要添加到数据库的教具选项数组
         */
        $new_settings = $data['post']['baby_character_setting_id'] ?: [];
        $need_remove_setting_id = [];
        $need_add_setting_id = [];

        $find_exist_options_sql = "SELECT setting_id FROM ".self::$dp."user_baby_character WHERE `baby_id` = :baby_id";
        $exist_tools = self::$db->get_all($find_exist_options_sql, ['baby_id'=>$data['post']['baby_id']]);

        #如果已经存在了教具的选项
        if (!empty($exist_tools)) {
            foreach ($exist_tools as $exist) {
                #如果这个数据库中存在的教具选项 不在 表单提交的教具选项数组中,说明此选项已经被删除,添加到删除数组中
                if (!in_array($exist['setting_id'], $new_settings)) {
                    array_push($need_remove_setting_id, $exist['setting_id']);
                } else {
                    #如果这个数据库中存在的教具选项 存在 表单提交的教具选项数组中,
                    #那么把此数据库中的教具选项从表单提交的教具选项数组中删除
                    $key = array_search($exist['setting_id'], $new_settings);
                    unset($new_settings[$key]);
                }
            }

            #通过上述判断筛选完成以后,如果表单提交的教具数组中还有值,那么添加到需要添加到数据库的教具数组中
            if (!empty($new_settings)) {
                foreach ($new_settings as $new) {
                    array_push($need_add_setting_id, $new);
                }
            }
        } elseif(!empty($new_settings)) {
            #如果之前不存在教具的选项,直接把表单提交的教具选项添加到数据库的教具数组中
            foreach ($new_settings as $new) {
                array_push($need_add_setting_id, $new);
            }
        }

        /**
         * 后续执行需要添加和需要删除的操作
         */
        if (!empty($need_remove_setting_id)) {
            $need_remove_sql = "DELETE FROM ".self::$dp."user_baby_character " .
                " WHERE `baby_id` = :baby_id " .
                " AND   `setting_id` = :setting_id ";

            foreach ($need_remove_setting_id as $remove) {
                self::$db->delete(
                    $need_remove_sql,
                    [
                        'baby_id'        => $data['post']['baby_id'],
                        'setting_id'    => $remove
                    ]
                );
            }
        }

        if (!empty($need_add_setting_id)) {
            $need_add_sql = "INSERT INTO ".self::$dp."user_baby_character " .
                " (`baby_id`,`setting_id`) VALUES  ";

            $options = [];
            foreach ($need_add_setting_id as $key=>$add) {
                $options[$key][0] = $data['post']['baby_id'];
                $options[$key][1] = $add;
            }

            self::$db->insert($need_add_sql, $options);
        }
    }

    public function removeBaby($data)
    {
        $sql = "UPDATE ".self::$dp."user_baby SET `deleted`=2 WHERE `baby_id` = :baby_id";

        return self::$db->update($sql, ['baby_id'=>$data['baby_id']]);
    }

    public function removeBabys($data)
    {
        $sql = "UPDATE ".self::$dp."user_baby SET `deleted`=2 WHERE `baby_id` = :baby_id";

        foreach ($data['baby_ids'] as $baby_id) {
            self::$db->update($sql, ['baby_id'=>$baby_id]);
        }

        return true;
    }
}
