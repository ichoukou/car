<?php
namespace Admin\Model\Teaching;

use Libs\Core\DbFactory AS DbFactory;

class Teaching extends DbFactory
{
    public function autocompleteFindBabys($data)
    {
        $conditions = [];

        $sql = "SELECT b.baby_id,b.name,b.user_id,u.parent_name,u.tel FROM ".self::$dp."user_baby AS b".
            " LEFT JOIN ".self::$dp."user AS u ON b.user_id=u.user_id ".
            " WHERE b.`deleted` = 1 AND u.deleted = 1";

        if (!empty($data['post']['filter_param'])) {
            $sql .= "AND b.`name` LIKE :name ";
            $conditions['name'] = "%".$data['post']['filter_param']."%";
        }

        if (count($conditions) <= 0) {
            $sql .= ' LIMIT 10';
        }

        $result = self::$db->get_all($sql, $conditions);

        /**
         * 找到会员卡种相关信息和会员宝宝敏感期数据
         */
        if (!empty($result)) {
            $date = date('Y-m-d');
            foreach ($result as $key=>$baby) {
                #会员卡种信息
                $find_user_card_sql = " SELECT uc.user_card_id,uc.card_number,uc.remaining_times,card_start_time,card_end_time,s.value FROM ".self::$dp."user_card AS uc " .
                    " LEFT JOIN ".self::$dp."setting AS s ON uc.setting_id = s.setting_id " .
                    " WHERE uc.`user_id` = :user_id AND uc.remaining_times > 0 AND uc.card_end_time > '{$date}' AND uc.`deleted` = 1";

                $user_card_list = self::$db->get_all($find_user_card_sql, ['user_id'=>$baby['user_id']]);

                if (!empty($user_card_list)) {
                    $result[$key]['user_card_list'] = $user_card_list;
                }
            }
        }

        return $result;
    }

    public function autocompleteFindSensitivePeriodSettings($data)
    {
        $result = '';
        $conditions = [];

        if (!empty($data['post']['module'])) {
            $sql = "SELECT * FROM ".self::$dp."setting_category WHERE `module` = :module AND `deleted` = 1";
            $setting_category = self::$db->get_one($sql, ['module'=>'baby_sensitive_period']);

            if (!empty($setting_category)) {
                $setting_sql = "SELECT `setting_id`,value,times FROM ".self::$dp."setting WHERE `setting_category_id` = :setting_category_id AND `deleted` = 1";

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

    public function autocompleteFindTeachingToolsSettings($data)
    {
        $result = '';
        $key = '';

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

    public function autocompleteFindUsers($data)
    {
        $conditions = [];

        $sql = "SELECT * FROM ".self::$dp."user WHERE `deleted` = 1 ";

        if (!empty($data['post']['filter_param'])) {
            $sql .= "AND `parent_name` LIKE :parent_name ";
            $conditions['parent_name'] = "%".$data['post']['filter_param']."%";
        }

        if (count($conditions) <= 0) {
            $sql .= ' LIMIT 10';
        }

        return self::$db->get_all($sql, $conditions);
    }

    public function findTeachingByTeachingId($data)
    {
        $sql = "SELECT t.*,u.parent_name,u.tel,b.name,s.value FROM ".self::$dp."teaching AS t  " .
            " LEFT JOIN ".self::$dp."user AS u ON t.user_id = u.user_id " .
            " LEFT JOIN ".self::$dp."user_baby AS b ON t.baby_id = b.baby_id " .
            " LEFT JOIN ".self::$dp."setting AS s ON t.setting_id = s.setting_id " .
            " WHERE t.`deleted` = 1 AND t.`teaching_id` = :teaching_id";

        $result = self::$db->get_one($sql, ['teaching_id'=>$data['teaching_id']]);

        if (!empty($result)) {
            $find_teaching_tools_sql = "SELECT tt.*,s.value FROM ".self::$dp."teaching_tools AS tt " .
                                       " LEFT JOIN ".self::$dp."setting AS s ON tt.setting_id = s.setting_id " .
                                       " WHERE tt.`teaching_id` = :teaching_id  " ;
            $result['teaching_tools'] = self::$db->get_all($find_teaching_tools_sql, ['teaching_id'=>$result['teaching_id']]);

            $find_card_setting_sql = " SELECT uc.setting_id,uc.card_number,uc.remaining_times,uc.card_start_time,uc.card_end_time,s.value FROM ".self::$dp."user_card AS uc " .
                                     " LEFT JOIN ".self::$dp."setting AS s ON uc.setting_id = s.setting_id " .
                                     " WHERE uc.`user_card_id` = :user_card_id";
            $result['card_setting_info'] = self::$db->get_one($find_card_setting_sql, ['user_card_id'=>$result['user_card_id']]);
        }

        return $result;
    }

    public function findTeachings($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT t.*,u.parent_name,u.tel,uc.card_number,b.name,s.value,s.times FROM ".self::$dp."teaching AS t  " .
               " LEFT JOIN ".self::$dp."user AS u ON t.user_id = u.user_id " .
               " LEFT JOIN ".self::$dp."user_baby AS b ON t.baby_id = b.baby_id " .
               " LEFT JOIN ".self::$dp."user_card AS uc ON t.user_card_id = uc.user_card_id " .
               " LEFT JOIN ".self::$dp."setting AS s ON t.setting_id = s.setting_id " .
               " WHERE t.`deleted` = 1 ";

        if (!empty($params['filter_user_id'])) {
            $sql .= "AND t.`user_id` = :user_id ";
            $conditions['user_id'] = $params['filter_user_id'];
        }

        if (!empty($params['filter_baby_id'])) {
            $sql .= "AND t.`baby_id` = :baby_id ";
            $conditions['baby_id'] = $params['filter_baby_id'];
        }

//        if (!empty($params['filter_card_number'])) {
//            $sql .= "AND uc.`card_number` = :card_number ";
//            $conditions['card_number'] = $params['filter_card_number'];
//        }
//
//        if (!empty($params['filter_title'])) {
//            $sql .= "AND `title` LIKE :title ";
//            $conditions['title'] = "%".$params['filter_title']."%";
//        }
//
//        if (!empty($params['filter_teaching_tools_id'])) {
//            $sql .= "AND tt.`setting_id` LIKE :setting_id ";
//            $conditions['setting_id'] = "%".$params['filter_teaching_tools_id']."%";
//        }
//
//        if (!empty($params['filter_sensitive_period_id'])) {
//            $sql .= "AND t.`setting_id` = :setting_id ";
//            $conditions['setting_id'] = $params['filter_sensitive_period_id'];
//        }
//
//        if (!empty($params['filter_teaching_date'])) {
//            $sql .= "AND t.`teaching_date` = :teaching_date ";
//            $conditions['teaching_date'] = $params['filter_teaching_date'];
//        }
//
//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND t.`create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY t.teaching_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        if (!empty($result)) {
            foreach ($result as $key=>$teaching) {
                $find_teaching_tools_sql = "SELECT tt.*,s.value FROM ".self::$dp."teaching_tools AS tt " .
                    " LEFT JOIN ".self::$dp."setting AS s ON tt.setting_id = s.setting_id " .
                    " WHERE tt.`teaching_id` = :teaching_id  " ;
                $result[$key]['teaching_tools'] = self::$db->get_all($find_teaching_tools_sql, ['teaching_id'=>$teaching['teaching_id']]);
            }
        }

        return $result;
    }

    public function findTeachingsCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."teaching AS t  " .
            " LEFT JOIN ".self::$dp."user AS u ON t.user_id = u.user_id " .
            " LEFT JOIN ".self::$dp."user_baby AS b ON t.baby_id = b.baby_id " .
            " LEFT JOIN ".self::$dp."user_card AS uc ON t.user_card_id = uc.user_card_id " .
            " WHERE t.`deleted` = 1 ";

        if (!empty($params['filter_user_id'])) {
            $sql .= "AND t.`user_id` = :user_id ";
            $conditions['user_id'] = $params['filter_user_id'];
        }

        if (!empty($params['filter_baby_id'])) {
            $sql .= "AND t.`baby_id` = :baby_id ";
            $conditions['baby_id'] = $params['filter_baby_id'];
        }

//        if (!empty($params['filter_card_number'])) {
//            $sql .= "AND uc.`card_number` = :card_number ";
//            $conditions['card_number'] = $params['filter_card_number'];
//        }
//
//        if (!empty($params['filter_title'])) {
//            $sql .= "AND `title` LIKE :title ";
//            $conditions['title'] = "%".$params['filter_title']."%";
//        }
//
//        if (!empty($params['filter_teaching_tools_id'])) {
//            $sql .= "AND tt.`setting_id` LIKE :setting_id ";
//            $conditions['setting_id'] = "%".$params['filter_teaching_tools_id']."%";
//        }
//
//        if (!empty($params['filter_sensitive_period_id'])) {
//            $sql .= "AND t.`setting_id` = :setting_id ";
//            $conditions['setting_id'] = $params['filter_sensitive_period_id'];
//        }
//
//        if (!empty($params['filter_teaching_date'])) {
//            $sql .= "AND t.`teaching_date` = :teaching_date ";
//            $conditions['teaching_date'] = $params['filter_teaching_date'];
//        }
//
//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND t.`create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addTeaching($data)
    {
        $find_user_info_sql = $sql = "SELECT user_id FROM ".self::$dp."user_baby WHERE `baby_id` = :baby_id ";
        $user_info = self::$db->get_one($find_user_info_sql, ['baby_id'=>$data['post']['baby_id']]);

        $find_setting_info_sql = $sql = "SELECT times FROM ".self::$dp."setting WHERE `setting_id` = :setting_id ";
        $setting_info = self::$db->get_one($find_setting_info_sql, ['setting_id'=>$data['post']['setting_id']]);

        $find_user_card_sql = $sql = "SELECT remaining_times FROM ".self::$dp."user_card WHERE `user_id` = :user_id ";
        $user_card_info = self::$db->get_one($find_user_card_sql, ['user_id'=>$user_info['user_id']]);

        #没有找到会员信息和会员卡种信息
        if (empty($user_info) or empty($setting_info))
            return -1;

        #会员卡里剩余课时不够
        if ($user_card_info['remaining_times'] - $setting_info['times'] < 0)
            return -2;

        /**
         * 添加上课记录
         */
        $sql = "INSERT INTO ".self::$dp."teaching (`user_id`,`baby_id`,`user_card_id`,`title`,`setting_id`,`teaching_date`,`teaching_start_time`,`teaching_end_time`) VALUES ";
        $teaching_id = self::$db->insert(
            $sql,
            [
                $user_info['user_id'],
                $data['post']['baby_id'],
                $data['post']['user_card_id'],
                $data['post']['title'],
                $data['post']['setting_id'],
                $data['post']['teaching_date'],
                $data['post']['teaching_start_time'],
                $data['post']['teaching_end_time']
            ]
        );

        /**
         * 减一次用户卡种使用次数
         */
        $update_user_card_sql = "UPDATE ".self::$dp."user_card SET `remaining_times` = remaining_times - {$setting_info['times']} WHERE `user_card_id` = :user_card_id AND `remaining_times` > 0 ";
        self::$db->update($update_user_card_sql, ['user_card_id'=>$data['post']['user_card_id']]);

        /**
         * 添加上课教具
         */
        if (count($data['post']['teaching_tool_setting_id']) > 0) {
            $insert_teaching_tools_sql = "INSERT INTO ".self::$dp."teaching_tools (`teaching_id`,`setting_id`) VALUES ";
            $teaching_tool_options = [];
            foreach ($data['post']['teaching_tool_setting_id'] as $teaching_tool_key=>$teaching_tool_setting_id) {
                $teaching_tool_options[$teaching_tool_key][0] = $teaching_id;
                $teaching_tool_options[$teaching_tool_key][1] = $teaching_tool_setting_id;
            }
            self::$db->insert($insert_teaching_tools_sql, $teaching_tool_options);
        }

        return $teaching_id;
    }

    public function editTeaching($data)
    {
        $update_sql = "UPDATE " . self::$dp . "teaching SET " .
            " `title` = :title, `teaching_date` = :teaching_date, " .
            " `teaching_start_time` = :teaching_start_time, `teaching_end_time` = :teaching_end_time " .
            " WHERE `teaching_id` = :teaching_id";

        self::$db->update(
            $update_sql,
            [
                'teaching_id'           => $data['post']['teaching_id'],
                'title'                 => $data['post']['title'],
                'teaching_date'         => $data['post']['teaching_date'],
                'teaching_start_time' => $data['post']['teaching_start_time'],
                'teaching_end_time'   => $data['post']['teaching_end_time']
            ]
        );

        /**
         * $new_teaching_tools 表单提交的教具选项数组
         * $need_remove_setting_id 需要从数据库删除的教具选项数组
         * $need_add_setting_id 需要添加到数据库的教具选项数组
         */
        $new_teaching_tools = $data['post']['teaching_tool_setting_id'] ?: [];
        $need_remove_setting_id = [];
        $need_add_setting_id = [];

        $find_exist_options_sql = "SELECT setting_id FROM ".self::$dp."teaching_tools WHERE `teaching_id` = :teaching_id";
        $exist_tools = self::$db->get_all($find_exist_options_sql, ['teaching_id'=>$data['post']['teaching_id']]);

        #如果已经存在了教具的选项
        if (!empty($exist_tools)) {
            foreach ($exist_tools as $exist) {
                #如果这个数据库中存在的教具选项 不在 表单提交的教具选项数组中,说明此选项已经被删除,添加到删除数组中
                if (!in_array($exist['setting_id'], $new_teaching_tools)) {
                    array_push($need_remove_setting_id, $exist['setting_id']);
                } else {
                    #如果这个数据库中存在的教具选项 存在 表单提交的教具选项数组中,
                    #那么把此数据库中的教具选项从表单提交的教具选项数组中删除
                    $key = array_search($exist['setting_id'], $new_teaching_tools);
                    unset($new_teaching_tools[$key]);
                }
            }

            #通过上述判断筛选完成以后,如果表单提交的教具数组中还有值,那么添加到需要添加到数据库的教具数组中
            if (!empty($new_teaching_tools)) {
                foreach ($new_teaching_tools as $new) {
                    array_push($need_add_setting_id, $new);
                }
            }
        } elseif(!empty($new_teaching_tools)) {
            #如果之前不存在教具的选项,直接把表单提交的教具选项添加到数据库的教具数组中
            foreach ($new_teaching_tools as $new) {
                array_push($need_add_setting_id, $new);
            }
        }

        /**
         * 后续执行需要添加和需要删除的操作
         */
        if (!empty($need_remove_setting_id)) {
            $need_remove_sql = "DELETE FROM ".self::$dp."teaching_tools " .
                " WHERE `teaching_id` = :teaching_id " .
                " AND   `setting_id` = :setting_id ";

            foreach ($need_remove_setting_id as $remove) {
                self::$db->delete(
                    $need_remove_sql,
                    [
                        'teaching_id'        => $data['post']['teaching_id'],
                        'setting_id'         => $remove
                    ]
                );
            }
        }

        if (!empty($need_add_setting_id)) {
            $need_add_sql = "INSERT INTO ".self::$dp."teaching_tools " .
                " (`teaching_id`,`setting_id`) VALUES  ";

            $options = [];
            foreach ($need_add_setting_id as $key=>$add) {
                $options[$key][0] = $data['post']['teaching_id'];
                $options[$key][1] = $add;
            }

            self::$db->insert($need_add_sql, $options);
        }
    }

    public function removeTeaching($data)
    {
        $sql = "UPDATE ".self::$dp."teaching SET `deleted`=2 WHERE `teaching_id` = :teaching_id";

        return self::$db->update($sql, ['teaching_id'=>$data['teaching_id']]);
    }

    public function removeTeachings($data)
    {
        $sql = "UPDATE ".self::$dp."teaching SET `deleted`=2 WHERE `teaching_id` = :teaching_id";

        foreach ($data['teaching_ids'] as $teaching_id) {
            self::$db->update($sql, ['teaching_id'=>$teaching_id]);
        }

        return true;
    }
}
