<?php
namespace Admin\Model\User;

use Libs\Core\DbFactory AS DbFactory;

class ContinuedCard extends DbFactory
{
    public function findCardValidPeriods($data)
    {
        $sql = "SELECT months,times FROM ".self::$dp."setting WHERE `setting_id` = :setting_id AND `deleted` = 1";

        $setting_info = self::$db->get_one($sql, ['setting_id'=>$data['setting_id']]);

        return $setting_info;
    }

    public function findUserCardByUserCardId($data)
    {
        $sql = " SELECT uc.*,u.parent_name,u.tel,s.value FROM ".self::$dp."user_card AS uc " .
            " LEFT JOIN ".self::$dp."user AS u ON uc.user_id = u.user_id " .
            " LEFT JOIN ".self::$dp."setting AS s ON uc.setting_id = s.setting_id " .
            " WHERE uc.`deleted` = 1 AND uc.`user_card_id` = :user_card_id";

        return self::$db->get_one($sql, ['user_card_id'=>$data['user_card_id']]);
    }

    public function findUserContinuedCardByUserContinuedCardId($data)
    {
        $sql = " SELECT ucc.*,u.parent_name,u.tel,s.value FROM ".self::$dp."user_continued_card AS ucc " .
            " LEFT JOIN ".self::$dp."user AS u ON ucc.user_id = u.user_id " .
            " LEFT JOIN ".self::$dp."setting AS s ON ucc.setting_id = s.setting_id " .
            " WHERE ucc.`deleted` = 1 AND ucc.`user_continued_card_id` = :user_continued_card_id";

        $result = self::$db->get_one($sql, ['user_continued_card_id'=>$data['user_continued_card_id']]);

        $find_log_sql = " SELECT uccl.*,s.value FROM ".self::$dp."user_continued_card_log AS uccl " .
            " LEFT JOIN ".self::$dp."setting AS s ON uccl.setting_id = s.setting_id " .
            " WHERE uccl.`user_continued_card_id` = :user_continued_card_id";

        $result['log'] = self::$db->get_one($find_log_sql, ['user_continued_card_id'=>$data['user_continued_card_id']]);

        return $result;
    }

    public function findContinuedCards($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit'],
            'user_card_id' => $params['user_card_id']
        ];

        $sql = " SELECT * FROM ".self::$dp."user_continued_card AS ucc WHERE user_card_id = :user_card_id AND `deleted` = 1 ";

        if (!empty($params['filter_card_start_time'])) {
            $sql .= "AND `card_start_time` LIKE :card_start_time ";
            $conditions['card_start_time'] = "%".$params['filter_card_start_time']."%";
        }

        if (!empty($params['filter_card_end_time'])) {
            $sql .= "AND `card_end_time` LIKE :card_end_time ";
            $conditions['card_end_time'] = "%".$params['filter_card_end_time']."%";
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY user_continued_card_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findContinuedCardsCount($data)
    {
        $params = $data['params'];

        $conditions = [
            'user_card_id' => $params['user_card_id']
        ];

        $sql = " SELECT COUNT(*) AS total FROM ".self::$dp."user_continued_card WHERE user_card_id = :user_card_id AND `deleted` = 1 ";

        if (!empty($params['filter_card_start_time'])) {
            $sql .= "AND `card_start_time` LIKE :card_start_time ";
            $conditions['card_start_time'] = "%".$params['filter_card_start_time']."%";
        }

        if (!empty($params['filter_card_end_time'])) {
            $sql .= "AND `card_end_time` LIKE :card_end_time ";
            $conditions['card_end_time'] = "%".$params['filter_card_end_time']."%";
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addContinuedCard($data)
    {
        $data = $data['post'];

        /*
         * 找到原卡种信息
         */
        $find_user_card_sql = " SELECT * FROM ".self::$dp."user_card " .
               " WHERE `deleted` = 1 AND `user_card_id` = :user_card_id";
        $user_card_info = self::$db->get_one($find_user_card_sql, ['user_card_id'=>$data['user_card_id']]);

        if (empty($user_card_info))
            return -1;

        /*
       * 找到当前卡的最大续卡次数
       */
        $find_continued_sql = " SELECT MAX(times) AS times FROM ".self::$dp."user_continued_card " .
            " WHERE `user_card_id` = :user_card_id LIMIT 1";
        $continued_info = self::$db->get_one($find_continued_sql, ['user_card_id'=>$data['user_card_id']]);

        /*
         * 添加续卡记录sql
         */
        $sql = "INSERT INTO ".self::$dp."user_continued_card " .
            " (`user_id`,`baby_id`,`user_card_id`,`card_number`,`setting_id`,`remaining_times`,`card_start_time`,`card_end_time`,`money`,`times`) VALUES ";

        /*
         * 先判断此卡是否续过卡,如果未续过卡,先备份卡本身的记录
         */
        if ($continued_info['times'] == 0) {
            self::$db->insert(
                $sql,
                [
                    $user_card_info['user_id'],
                    $user_card_info['baby_id'],
                    $user_card_info['user_card_id'],
                    $user_card_info['card_number'],
                    $user_card_info['setting_id'],
                    $user_card_info['remaining_times'],
                    $user_card_info['card_start_time'],
                    $user_card_info['card_end_time'],
                    0,
                    0
                ]
            );
        }

        /*
         * 添加一条续卡记录
         */
        $new_remaining_times = $user_card_info['remaining_times'] + (int)$data['remaining_times'];
        self::$db->insert(
            $sql,
            [
                $user_card_info['user_id'],
                $user_card_info['baby_id'],
                $user_card_info['user_card_id'],
                $user_card_info['card_number'],
                $user_card_info['setting_id'],
                $new_remaining_times,
                $user_card_info['card_start_time'],
                $data['card_end_time'],
                $data['money'],
                $continued_info['times'] + 1
            ]
        );

        /*
        * 添加一条续卡记录详情日志,记录时间变化,使用次数变化
        */
        $find_continued_last_sql = " SELECT user_continued_card_id FROM ".self::$dp."user_continued_card " .
            " WHERE `user_card_id` = :user_card_id ORDER BY user_continued_card_id DESC LIMIT 1";
        $continued_last_id = self::$db->get_one($find_continued_last_sql, ['user_card_id'=>$data['user_card_id']]);

        $insert_log_sql = "INSERT INTO ".self::$dp."user_continued_card_log " .
            " (`user_continued_card_id`,`user_card_id`,`setting_id`,`add_remaining_times`,`money`,`continued_card_start_time`,`continued_card_end_time`) VALUES ";
        self::$db->insert(
            $insert_log_sql,
            [
                $continued_last_id['user_continued_card_id'],
                $user_card_info['user_card_id'],
                $data['valid_period_setting_id'],
                (int)$data['remaining_times'],
                $data['money'],
                $user_card_info['card_end_time'],
                $data['card_end_time'],
            ]
        );

        /*
         * 更新原卡种续卡后的信息
         */
        $update_card_sql = "UPDATE ".self::$dp."user_card SET " .
            " `remaining_times` = :remaining_times, " .
            " `card_end_time` = :card_end_time, " .
            " `times` = times + 1 " .
            " WHERE `user_card_id` = :user_card_id ";
        self::$db->update(
            $update_card_sql,
            [
                'remaining_times'   => $new_remaining_times,
                'card_end_time'     => $data['card_end_time'],
                'user_card_id'      => $user_card_info['user_card_id']
            ]
        );

        return 1;
    }

    public function editContinuedCard($data)
    {
        $update_sql = " UPDATE " . self::$dp . "user_continued_card SET" .
            " `money` = :money " .
            " WHERE `user_continued_card_id` = :user_continued_card_id ";

        self::$db->update(
            $update_sql,
            [
                'user_continued_card_id'      => $data['post']['user_continued_card_id'],
                'money'             => $data['post']['money'],
            ]
        );
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
