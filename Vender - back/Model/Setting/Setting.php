<?php
namespace Admin\Model\Setting;

use Libs\Core\DbFactory AS DbFactory;

class Setting extends DbFactory
{
    public function findSettingBySettingId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."setting WHERE `setting_id` = :setting_id";

        return self::$db->get_one($sql, ['setting_id'=>$data['setting_id']]);
    }

    public function findSettings($data)
    {
        $params = $data['params'];

        $conditions = [
            'start'                 => $params['start'],
            'limit'                 => $params['limit'],
            'setting_category_id'   => $params['setting_category_id'],
            'deleted'               => 1
        ];

        $sql = "SELECT * FROM ".self::$dp."setting WHERE `setting_category_id` = :setting_category_id AND `deleted` = :deleted ";

        if (!empty($params['filter_value'])) {
            $sql .= "AND `value` LIKE :value ";
            $conditions['value'] = "%".$params['filter_value']."%";
        }

        if (!empty($params['filter_deleted'])) {
            $conditions['deleted'] = $params['filter_deleted'];
        }

        $sql .= " ORDER BY setting_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findSettingCount($data)
    {
        $params = $data['params'];

        $conditions = [
            'setting_category_id' => $params['setting_category_id'],
            'deleted'             => 1
        ];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."setting WHERE `setting_category_id` = :setting_category_id AND `deleted` = :deleted ";

        if (!empty($params['filter_value'])) {
            $sql .= "AND `value` LIKE :value ";
            $conditions['value'] = "%".$params['filter_value']."%";
        }

        if (!empty($params['filter_deleted'])) {
            $conditions['deleted'] = $params['filter_deleted'];
        }

        $conditions['setting_category_id'] = "{$params['setting_category_id']}";

        return self::$db->count($sql, $conditions);
    }

    public function addSetting($data)
    {
        $sql = "SELECT * FROM ".self::$dp."setting WHERE `setting_category_id` = :setting_category_id AND `key` = :key ";
        $exists = self::$db->get_one(
            $sql,
            [
                'setting_category_id'   => $data['post']['setting_category_id'],
                'key'                   => $data['post']['key']
            ]
        );

        if (!empty($exists))
            return -1;

        $param = [
            $data['post']['setting_category_id'],
            $data['post']['key'],
            $data['post']['value']
        ];

        $splice_sql = '';

        if (isset($data['post']['times'])) {
            $times = (int)$data['post']['times'];
            $splice_sql .= ' ,`times` ';
            array_push($param, $times);
        }

        if (!empty($data['post']['parent_setting_id'])) {
            $splice_sql .= ' ,`parent_setting_id` ';
            array_push($param, $data['post']['parent_setting_id']);
        }

        if (!empty($data['post']['money'])) {
            $splice_sql .= ' ,`money` ';
            array_push($param, $data['post']['money']);
        }

        if (!empty($data['post']['description'])) {
            $splice_sql .= ' ,`description` ';
            array_push($param, $data['post']['description']);
        }

        if (isset($data['post']['sort'])) {
            $splice_sql .= ' ,`sort` ';
            array_push($param, (int)$data['post']['sort']);
        }

        if (!empty($data['post']['months'])) {
            $splice_sql .= ' ,`months` ';
            array_push($param, $data['post']['months']);
        }

        $sql = "INSERT INTO ".self::$dp."setting (`setting_category_id`,`key`,`value` {$splice_sql}) VALUES ";

        return self::$db->insert($sql, $param);
    }

    public function editSetting($data)
    {

        $sql = "SELECT * FROM ".self::$dp."setting WHERE `setting_category_id` = :setting_category_id AND `key` = :key AND `setting_id` != :setting_id ";
        $exists = self::$db->get_one(
            $sql,
            [
                'setting_category_id'   => $data['post']['setting_category_id'],
                'key'                   => $data['post']['key'],
                'setting_id'            => $data['post']['setting_id']
            ]
        );

        if (!empty($exists))
            return -1;

        $param = [
            'value'        => $data['post']['value'],
            'key'          => $data['post']['key'],
            'setting_id'   => $data['post']['setting_id']
        ];

        $splice_sql = '';

        if (isset($data['post']['times'])) {
            $times = (int)$data['post']['times'];
            $splice_sql .= ' `times` = :times, ';
            $param['times'] = $times;
        }

        if (!empty($data['post']['parent_setting_id'])) {
            $splice_sql .= ' `parent_setting_id` = :parent_setting_id, ';
            $param['parent_setting_id'] = $data['post']['parent_setting_id'];
        }

        if (!empty($data['post']['money'])) {
            $splice_sql .= ' `money` = :money, ';
            $param['money'] = $data['post']['money'];
        }

        if (!empty($data['post']['description'])) {
            $splice_sql .= ' `description` = :description, ';
            $param['description'] = $data['post']['description'];
        }

        if (isset($data['post']['sort'])) {
            $splice_sql .= ' `sort` = :sort, ';
            $param['sort'] = (int)$data['post']['sort'];
        }

        if (!empty($data['post']['months'])) {
            $splice_sql .= ' `months` = :months, ';
            $param['months'] = $data['post']['months'];
        }

        $update_sql = "UPDATE " . self::$dp . "setting SET"
            ." {$splice_sql} "
            ." `value` = :value, `key` = :key "
            ." WHERE `setting_id` = :setting_id";

        self::$db->update($update_sql, $param);
    }

    public function removeSetting($data)
    {
        $sql = "UPDATE ".self::$dp."setting SET `deleted` = 2 WHERE `setting_id` = :setting_id";

        return self::$db->update($sql, ['setting_id'=>$data['setting_id']]);
    }

    public function removeSettings($data)
    {
        $sql = "UPDATE ".self::$dp."setting SET `deleted` = 2 WHERE `setting_id` = :setting_id";

        foreach ($data['setting_ids'] as $setting_id) {
            self::$db->update($sql, ['setting_id'=>$setting_id]);
        }

        return true;
    }
}
