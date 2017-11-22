<?php
namespace Admin\Model\User;

use Libs\Core\DbFactory AS DbFactory;

class Card extends DbFactory
{
    public function findCardTypes($data)
    {
        $sql = "SELECT setting_category_id FROM ".self::$dp."setting_category WHERE `key` = :key AND `deleted` = 1";
        $setting_category = self::$db->get_one($sql, ['key'=>$data['module_key']]);

        $result = '';
        if (!empty($setting_category)) {
            $setting_sql = "SELECT s1.setting_id,s1.money,s1.`value`,s1.times,s2.months FROM ".self::$dp."setting As s1" .
                           " LEFT JOIN ".self::$dp."setting AS s2 ON s1.parent_setting_id=s2.setting_id " .
                           " WHERE s1.`setting_id` = :setting_id AND s1.`deleted` = 1 AND s2.`setting_category_id` = :setting_category_id ";

            $settings = self::$db->get_one(
                $setting_sql,
                [
                    'setting_category_id'=>$setting_category['setting_category_id'],
                    'setting_id'=>$data['setting_id']
                ]
            );

            $result = $settings;
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

    public function findUserCardByUserCardId($data)
    {
        $sql = " SELECT uc.*,u.parent_name,u.tel FROM ".self::$dp."user_card AS uc " .
            " LEFT JOIN ".self::$dp."user AS u ON uc.user_id = u.user_id " .
            " WHERE uc.`deleted` = 1 AND uc.`user_card_id` = :user_card_id";

        return self::$db->get_one($sql, ['user_card_id'=>$data['user_card_id']]);
    }

    public function findCards($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = " SELECT uc.*,u.parent_name,u.tel,ub.name FROM ".self::$dp."user_card AS uc " .
               " LEFT JOIN ".self::$dp."user AS u ON uc.user_id = u.user_id " .
                " LEFT JOIN ".self::$dp."user_baby AS ub ON uc.baby_id = ub.baby_id " .
               " WHERE uc.`deleted` = 1 ";

        if (!empty($params['filter_baby_id'])) {
            $sql .= "AND uc.`baby_id` = :baby_id ";
            $conditions['baby_id'] = $params['filter_baby_id'];
        }

//        if (!empty($params['filter_card_number'])) {
//            $sql .= "AND uc.`card_number` = :card_number ";
//            $conditions['card_number'] = $params['filter_card_number'];
//        }
//
//        if (!empty($params['filter_setting_id'])) {
//            $sql .= "AND uc.`setting_id` = :setting_id ";
//            $conditions['setting_id'] = $params['filter_setting_id'];
//        }
//
//        if (isset($params['filter_times']) && is_numeric($params['filter_times'])) {
//            $sql .= "AND uc.`times` = :times ";
//            $conditions['times'] = $params['filter_times'];
//        }
//
//        if (!empty($params['filter_card_start_time'])) {
//            $sql .= "AND uc.`card_start_time` LIKE :card_start_time ";
//            $conditions['card_start_time'] = "%".$params['filter_card_start_time']."%";
//        }
//
//        if (!empty($params['filter_card_end_time'])) {
//            $sql .= "AND uc.`card_end_time` LIKE :card_end_time ";
//            $conditions['card_end_time'] = "%".$params['filter_card_end_time']."%";
//        }
//
//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND uc.`create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY uc.user_card_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findCardsCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = " SELECT COUNT(*) AS total FROM ".self::$dp."user_card AS uc " .
            #" LEFT JOIN ".self::$dp."user AS u ON uc.user_id = u.user_id " .
            " WHERE uc.`deleted` = 1 ";

        if (!empty($params['filter_baby_id'])) {
            $sql .= "AND uc.`baby_id` = :baby_id ";
            $conditions['baby_id'] = $params['filter_baby_id'];
        }

//        if (!empty($params['filter_card_number'])) {
//            $sql .= "AND uc.`card_number` = :card_number ";
//            $conditions['card_number'] = $params['filter_card_number'];
//        }
//
//        if (!empty($params['filter_setting_id'])) {
//            $sql .= "AND uc.`setting_id` = :setting_id ";
//            $conditions['setting_id'] = $params['filter_setting_id'];
//        }
//
//        if (isset($params['filter_times']) && is_numeric($params['filter_times'])) {
//            $sql .= "AND uc.`times` = :times ";
//            $conditions['times'] = $params['filter_times'];
//        }
//
//        if (!empty($params['filter_card_start_time'])) {
//            $sql .= "AND uc.`card_start_time` LIKE :card_start_time ";
//            $conditions['card_start_time'] = "%".$params['filter_card_start_time']."%";
//        }
//
//        if (!empty($params['filter_card_end_time'])) {
//            $sql .= "AND uc.`card_end_time` LIKE :card_end_time ";
//            $conditions['card_end_time'] = "%".$params['filter_card_end_time']."%";
//        }
//
//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND uc.`create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addCard($data)
    {
        $find_user_sql = " SELECT user_id FROM ".self::$dp."user_baby WHERE `baby_id` = :baby_id";
        $baby_user_info = self::$db->get_one($find_user_sql, ['baby_id'=>$data['post']['baby_id']]);
        if (empty($baby_user_info))
            return -1;

        $date = date("Ymd", time());

        $find_today_last_card_number_sql =
            " SELECT card_number FROM ".self::$dp."user_card " .
            " WHERE `card_number` LIKE :card_number " .
            " ORDER BY user_card_id DESC LIMIT 1";

        $today_last_card = self::$db->get_one(
            $find_today_last_card_number_sql,
            [
                'card_number' => $date."%"
            ]
        );

        if (empty($today_last_card['card_number'])) {
            $card_number = $date . '0001';
        } else {
            $number = substr($today_last_card['card_number'], 8) + 1;
            $card_number = $date . str_pad((int)$number, 4, 0, STR_PAD_LEFT);
        }

        $remaining_times = (int)$data['post']['remaining_times'];

        $sql = "INSERT INTO ".self::$dp."user_card (`user_id`,`baby_id`,`card_number`,`setting_id`,`money`,`remaining_times`,`card_start_time`,`card_end_time`) VALUES ";

        return self::$db->insert(
            $sql,
            [
                $baby_user_info['user_id'],
                $data['post']['baby_id'],
                $card_number,
                $data['post']['setting_id'],
                $data['post']['money'],
                $remaining_times > 0 ? $remaining_times : 0,
                $data['post']['card_start_time'],
                $data['post']['card_end_time']
            ]
        );
    }

    public function editCard($data)
    {
        $remaining_times = (int)$data['post']['remaining_times'];

        $update_sql = " UPDATE " . self::$dp . "user_card SET" .
                      " `money` = :money, " .
                      " `remaining_times` = :remaining_times " .
                      " WHERE `user_card_id` = :user_card_id ";

        self::$db->update(
            $update_sql,
            [
                'user_card_id'      => $data['post']['user_card_id'],
                'money'             => $data['post']['money'],
                'remaining_times'   => $remaining_times > 0 ? $remaining_times : 0
            ]
        );

//        $update_sql = "UPDATE " . self::$dp . "user_card SET"
//            #." `user_id` = :user_id, "
//            ." `setting_id` = :setting_id, "
//            ." `remaining_times` = :remaining_times, "
//            ." `card_start_time` = :card_start_time, "
//            ." `card_end_time` = :card_end_time "
//            ." WHERE `user_card_id` = :user_card_id ";
//
//        self::$db->update(
//            $update_sql,
//            [
//                'user_card_id'      => $data['post']['user_card_id'],
//                #'user_id'           => $data['post']['user_id'],
//                'setting_id'        => $data['post']['setting_id'],
//                'remaining_times'   => $data['post']['remaining_times'],
//                'card_start_time'   => $data['post']['card_start_time'],
//                'card_end_time'     => $data['post']['card_end_time']
//            ]
//        );
    }

    public function removeCard($data)
    {
        $sql = "UPDATE ".self::$dp."user_card SET `deleted`=2 WHERE `user_card_id` = :user_card_id";

        return self::$db->update($sql, ['user_card_id'=>$data['user_card_id']]);
    }

    public function removeCards($data)
    {
        $sql = "UPDATE ".self::$dp."user_card SET `deleted`=2 WHERE `user_card_id` = :user_card_id";

        foreach ($data['user_card_ids'] as $user_card_id) {
            self::$db->update($sql, ['user_card_id'=>$user_card_id]);
        }

        return true;
    }
}
