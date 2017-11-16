<?php
namespace Admin\Model\User;

use Libs\Core\DbFactory AS DbFactory;

class BabySensitivePeriod extends DbFactory
{
    public function findSensitivePeriodBySensitivePeriodId($data)
    {
        $sql = "SELECT sp.*,b.name,u.parent_name,u.tel FROM ".self::$dp."user_baby_sensitive_period AS sp".
            " LEFT JOIN ".self::$dp."user AS u ON sp.user_id=u.user_id ".
            " LEFT JOIN ".self::$dp."user_baby AS b ON sp.baby_id=b.baby_id ".
            " WHERE sp.`deleted` = 1 AND sp.`sensitive_period_id` = :sensitive_period_id ";

        $result = self::$db->get_one($sql, ['sensitive_period_id'=>$data['sensitive_period_id']]);

        if (!empty($result)) {
            $find_options_sql = "SELECT bo.*,s.value FROM ".self::$dp."user_baby_sensitive_period_option AS bo " .
                " LEFT JOIN ".self::$dp."setting AS s ON bo.setting_id = s.setting_id " .
                " WHERE bo.`sensitive_period_id` = :sensitive_period_id  " ;
            $sensitive_periods = self::$db->get_all($find_options_sql, ['sensitive_period_id'=>$result['sensitive_period_id']]);

            $result['sensitive_period_options'] = $sensitive_periods;
        }

        return $result;
    }

    public function autocompleteFindBabys($data)
    {
        $conditions = [];

        $sql = "SELECT b.baby_id,b.name,u.parent_name,u.tel FROM ".self::$dp."user_baby AS b".
            " LEFT JOIN ".self::$dp."user AS u ON b.user_id=u.user_id ".
            " WHERE b.`deleted` = 1 AND u.deleted = 1";

        if (!empty($data['post']['filter_param'])) {
            $sql .= "AND b.`name` LIKE :name ";
            $conditions['name'] = "%".$data['post']['filter_param']."%";
        }

        if (count($conditions) <= 0) {
            $sql .= ' LIMIT 10';
        }

        return self::$db->get_all($sql, $conditions);
    }

    public function autocompleteFindSensitivePeriodSettings($data)
    {
        $result = '';
        $conditions = [];

        if (!empty($data['post']['module'])) {
            $sql = "SELECT * FROM ".self::$dp."setting_category WHERE `module` = :module AND `deleted` = 1";

            $setting_category = self::$db->get_one($sql, ['module'=>$data['post']['module']]);

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

    public function findSensitivePeriods($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT sp.*,b.name,u.parent_name,u.tel FROM ".self::$dp."user_baby_sensitive_period AS sp" .
               " LEFT JOIN ".self::$dp."user_baby_sensitive_period_option AS spo ON sp.sensitive_period_id=spo.sensitive_period_id " .
               " LEFT JOIN ".self::$dp."user AS u ON sp.user_id=u.user_id " .
               " LEFT JOIN ".self::$dp."user_baby AS b ON sp.baby_id=b.baby_id " .
               " WHERE sp.`deleted` = 1 ";

        if (!empty($params['filter_name'])) {
            $sql .= "AND b.`name` = :name ";
            $conditions['name'] = $params['filter_name'];
        }

        if (!empty($params['filter_parent_name'])) {
            $sql .= "AND u.`parent_name` = :parent_name ";
            $conditions['parent_name'] = $params['filter_parent_name'];
        }

//        if (!empty($params['filter_update_time'])) {
//            $sql .= "AND sp.`update_time` LIKE :update_time ";
//            $conditions['update_time'] = "%".$params['filter_update_time']."%";
//        }
//
//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND sp.`create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        if (!empty($params['filter_setting_id'])) {
            $sql .= "AND spo.`setting_id` = :setting_id ";
            $conditions['setting_id'] = $params['filter_setting_id'];
        }

        $sql .= " GROUP BY sp.sensitive_period_id ORDER BY sp.sensitive_period_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        if (!empty($result)) {
            foreach ($result as $k=>$sensitive_period) {
                $find_options_sql = "SELECT bo.*,s.value FROM ".self::$dp."user_baby_sensitive_period_option AS bo " .
                " LEFT JOIN ".self::$dp."setting AS s ON bo.setting_id = s.setting_id " .
                " WHERE bo.`sensitive_period_id` = :sensitive_period_id  " ;
                $sensitive_periods = self::$db->get_all($find_options_sql, ['sensitive_period_id'=>$sensitive_period['sensitive_period_id']]);
                $result[$k]['sensitive_period_options'] = $sensitive_periods;
            }
        }

        return $result;
    }

    public function findSensitivePeriodsCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT sp.*,b.name,u.parent_name FROM ".self::$dp."user_baby_sensitive_period AS sp" .
            " LEFT JOIN ".self::$dp."user_baby_sensitive_period_option AS spo ON sp.sensitive_period_id=spo.sensitive_period_id " .
            " LEFT JOIN ".self::$dp."user AS u ON sp.user_id=u.user_id " .
            " LEFT JOIN ".self::$dp."user_baby AS b ON sp.baby_id=b.baby_id " .
            " WHERE sp.`deleted` = 1 ";

        if (!empty($params['baby_id'])) {
            $sql .= "AND sp.`baby_id` = :baby_id ";
            $conditions['baby_id'] = $params['baby_id'];
        }

        if (!empty($params['filter_name'])) {
            $sql .= "AND b.`name` = :name ";
            $conditions['name'] = $params['filter_name'];
        }

        if (!empty($params['filter_parent_name'])) {
            $sql .= "AND u.`parent_name` = :parent_name ";
            $conditions['parent_name'] = $params['filter_parent_name'];
        }

        if (!empty($params['filter_setting_id'])) {
            $sql .= "AND spo.`setting_id` = :setting_id ";
            $conditions['setting_id'] = $params['filter_setting_id'];
        }

        return self::$db->count($sql, $conditions);
    }

    public function addBabySensitivePeriod($data)
    {
        $find_user_sql = "SELECT u.user_id FROM ".self::$dp."user_baby AS b " .
               " LEFT JOIN ".self::$dp."user AS u " .
               " ON b.user_id=u.user_id ".
               " WHERE b.`baby_id` = :baby_id ";
        $user_info = self::$db->get_one($find_user_sql, ['baby_id'=>$data['post']['baby_id']]);

        if (empty($user_info))
            return -1;

        $insert_sql = "INSERT INTO ".self::$dp."user_baby_sensitive_period (`user_id`,`baby_id`,`advantage`,`characteristic`,`habit`) VALUES ";
        $sensitive_period_id = self::$db->insert(
            $insert_sql,
            [
                $user_info['user_id'],
                $data['post']['baby_id'],
                $data['post']['advantage'],
                $data['post']['characteristic'],
                $data['post']['habit']
            ]
        );

        $insert_option_sql = "INSERT INTO ".self::$dp."user_baby_sensitive_period_option (`sensitive_period_id`,`setting_id`) VALUES ";
        if (count($data['post']['setting_id']) > 0) {
            $options = [];
            foreach ($data['post']['setting_id'] as $key=>$post_setting_id) {
                $options[$key][0] = $sensitive_period_id;
                $options[$key][1] = $post_setting_id;
            }
            $sensitive_period_id = self::$db->insert($insert_option_sql, $options);
        }

        return $sensitive_period_id;
    }

    public function editSensitivePeriod($data)
    {
        $update_sql = "UPDATE ".self::$dp."user_baby_sensitive_period SET " .
                      " `advantage` = :advantage, " .
                      " `characteristic` = :characteristic, " .
                      " `habit` = :habit " .
                      " WHERE `sensitive_period_id` = :sensitive_period_id ";

        self::$db->update(
            $update_sql,
            [
                'advantage'           => $data['post']['advantage'],
                'characteristic'      => $data['post']['characteristic'],
                'habit'                => $data['post']['habit'],
                'sensitive_period_id' => $data['post']['sensitive_period_id'],
            ]
        );

        /**
         * $new_sensitive_periods 表单提交的敏感期选项数组
         * $need_remove_setting_id 需要从数据库删除的敏感期选项数组
         * $need_add_setting_id 需要添加到数据库的敏感期选项数组
         */
        $new_sensitive_periods = $data['post']['setting_id'] ?: [];
        $need_remove_setting_id = [];
        $need_add_setting_id = [];

        $find_exist_options_sql = "SELECT setting_id FROM ".self::$dp."user_baby_sensitive_period_option WHERE `sensitive_period_id` = :sensitive_period_id";
        $exist_sensitive_periods = self::$db->get_all($find_exist_options_sql, ['sensitive_period_id'=>$data['post']['sensitive_period_id']]);

        #如果已经存在了敏感期的选项
        if (!empty($exist_sensitive_periods)) {
            foreach ($exist_sensitive_periods as $exist) {
                #如果这个数据库中存在的敏感期选项 不在 表单提交的敏感期选项数组中,说明此选项已经被删除,添加到删除数组中
                if (!in_array($exist['setting_id'], $new_sensitive_periods)) {
                    array_push($need_remove_setting_id, $exist['setting_id']);
                } else {
                    #如果这个数据库中存在的敏感期选项 存在 表单提交的敏感期选项数组中,
                    #那么把此数据库中的敏感期选项从表单提交的敏感期选项数组中删除
                    $key = array_search($exist['setting_id'], $new_sensitive_periods);
                    unset($new_sensitive_periods[$key]);
                }
            }

            #通过上述判断筛选完成以后,如果表单提交的敏感期数组中还有值,那么添加到需要添加到数据库的敏感期数组中
            if (!empty($new_sensitive_periods)) {
                foreach ($new_sensitive_periods as $new) {
                    array_push($need_add_setting_id, $new);
                }
            }
        } elseif(!empty($new_sensitive_periods)) {
            #如果之前不存在敏感期的选项,直接把表单提交的敏感期选项添加到数据库的敏感期数组中
            foreach ($new_sensitive_periods as $new) {
                array_push($need_add_setting_id, $new);
            }
        }

        /**
         * 后续执行需要添加和需要删除的操作
         */
        if (!empty($need_remove_setting_id)) {
            $need_remove_sql = "DELETE FROM ".self::$dp."user_baby_sensitive_period_option " .
                " WHERE `sensitive_period_id` = :sensitive_period_id " .
                " AND   `setting_id` = :setting_id ";

            foreach ($need_remove_setting_id as $remove) {
                self::$db->delete(
                    $need_remove_sql,
                    [
                        'sensitive_period_id' => $data['post']['sensitive_period_id'],
                        'setting_id'         => $remove
                    ]
                );
            }
        }

        if (!empty($need_add_setting_id)) {
            $need_add_sql = "INSERT INTO ".self::$dp."user_baby_sensitive_period_option " .
                            " (`sensitive_period_id`,`setting_id`) VALUES  ";

            $options = [];
            foreach ($need_add_setting_id as $key=>$add) {
                $options[$key][0] = $data['post']['sensitive_period_id'];
                $options[$key][1] = $add;
            }

            self::$db->insert($need_add_sql, $options);
        }
    }

    public function removeSensitivePeriod($data)
    {
        $sql = "UPDATE ".self::$dp."user_baby_sensitive_period SET `deleted`=2 WHERE `sensitive_period_id` = :sensitive_period_id";

        return self::$db->update($sql, ['sensitive_period_id'=>$data['sensitive_period_id']]);
    }

    public function removeSensitivePeriods($data)
    {
        $sql = "UPDATE ".self::$dp."user_baby_sensitive_period SET `deleted`=2 WHERE `sensitive_period_id` = :sensitive_period_id";

        foreach ($data['sensitive_period_ids'] as $sensitive_period_id) {
            self::$db->update($sql, ['sensitive_period_id'=>$sensitive_period_id]);
        }

        return true;
    }
}
