<?php
namespace Admin\Model\Record;

use Libs\Core\DbFactory AS DbFactory;

class Record extends DbFactory
{
    public function findRecordByRecordId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."record WHERE `record_id` = :record_id";

        return self::$db->get_one($sql, ['record_id'=>$data['record_id']]);
    }

    public function findRecords($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = " SELECT r.*,s.value FROM ".self::$dp."record AS r " .
               " LEFT JOIN ".self::$dp."setting AS s ON r.setting_id = s.setting_id " .
               " WHERE r.`deleted` = 1 ";

        if (!empty($params['filter_money'])) {
            $sql .= "AND r.`money` LIKE :money ";
            $conditions['money'] = "%".$params['filter_money']."%";
        }

        if (!empty($params['filter_setting_id'])) {
            $sql .= "AND r.`setting_id` = :setting_id ";
            $conditions['setting_id'] = $params['filter_setting_id'];
        }

        $sql .= " ORDER BY r.record_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findRecordsCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = " SELECT COUNT(*) AS total FROM ".self::$dp."record AS r " .
            " LEFT JOIN ".self::$dp."setting AS s ON r.setting_id = s.setting_id " .
            " WHERE r.`deleted` = 1 ";

        if (!empty($params['filter_money'])) {
            $sql .= "AND r.`money` LIKE :money ";
            $conditions['money'] = "%".$params['filter_money']."%";
        }

        if (!empty($params['filter_setting_id'])) {
            $sql .= "AND r.`setting_id` = :setting_id ";
            $conditions['setting_id'] = $params['filter_setting_id'];
        }

        return self::$db->count($sql, $conditions);
    }

    public function addRecord($data)
    {
        $sql = "INSERT INTO ".self::$dp."record (`setting_id`,`money`,`content`) VALUES ";

        return self::$db->insert(
            $sql,
            [
                $data['post']['setting_id'],
                $data['post']['money'],
                $data['post']['content']
            ]
        );
    }

    public function editRecord($data)
    {
        $update_sql = "UPDATE " . self::$dp . "record SET"
            ." `setting_id` = :setting_id, `money` = :money, "
            ." `content` = :content "
            ."  WHERE `record_id` = :record_id";

        self::$db->update(
            $update_sql,
            [
                'record_id'     => $data['post']['record_id'],
                'setting_id'       => $data['post']['setting_id'],
                'money'               => $data['post']['money'],
                'content'               => $data['post']['content']
            ]
        );
    }

    public function removeRecord($data)
    {
        $sql = "UPDATE ".self::$dp."record SET `deleted`=2 WHERE `record_id` = :record_id";

        return self::$db->update($sql, ['record_id'=>$data['record_id']]);
    }

    public function removeRecords($data)
    {
        $sql = "UPDATE ".self::$dp."record SET `deleted`=2 WHERE `record_id` = :record_id";

        foreach ($data['record_ids'] as $record_id) {
            self::$db->update($sql, ['record_id'=>$record_id]);
        }

        return true;
    }
}
