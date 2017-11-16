<?php
namespace Admin\Model\Setting;

use Libs\Core\DbFactory AS DbFactory;

class SettingCategory extends DbFactory
{
    public function findSettingCategoryByCategoryId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."setting_category WHERE `setting_category_id` = :setting_category_id";

        return self::$db->get_one($sql, ['setting_category_id'=>$data['setting_category_id']]);
    }

    public function findSettingCategorys($data)
    {
        $params = $data['params'];

        $conditions = [
            'start'     => $params['start'],
            'limit'     => $params['limit'],
            'module'    => $params['module'],
            'deleted'   => 1
        ];

        $sql = "SELECT * FROM ".self::$dp."setting_category WHERE `module` = :module AND `deleted` = :deleted ";

        if (!empty($params['filter_title'])) {
            $sql .= "AND `title` LIKE :title ";
            $conditions['title'] = "%".$params['filter_title']."%";
        }

        if (!empty($params['filter_deleted'])) {
            $conditions['deleted'] = $params['filter_deleted'];
        }

        $sql .= " ORDER BY setting_category_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findSettingCategoryCount($data)
    {
        $params = $data['params'];

        $conditions = [
            'module' => $params['module'],
            'deleted'   => 1
        ];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."setting_category WHERE `module` = :module AND `deleted` = :deleted ";

        if (!empty($params['filter_title'])) {
            $sql .= "AND `title` LIKE :title ";
            $conditions['title'] = "%".$params['filter_title']."%";
        }

        if (!empty($params['filter_deleted'])) {
            $conditions['deleted'] = $params['filter_deleted'];
        }

        return self::$db->count($sql, $conditions);
    }

    public function addSettingCategory($data)
    {
        $sql = "INSERT INTO ".self::$dp."setting_category (`module`,`key`,`title`,`description`) VALUES ";

        return self::$db->insert(
            $sql,
            [
                $data['post']['module'],
                $data['post']['key'],
                $data['post']['title'],
                $data['post']['description']
            ]
        );
    }

    public function editSettingCategory($data)
    {
        $update_sql = "UPDATE " . self::$dp . "setting_category SET"
            ." `key` = :key, "
            ." `title` = :title, "
            ." `description` = :description "
            ." WHERE `setting_category_id` = :setting_category_id";

        self::$db->update(
            $update_sql,
            [
                'key'                   => $data['post']['key'],
                'title'                 => $data['post']['title'],
                'description'                 => $data['post']['description'],
                'setting_category_id'   => $data['post']['setting_category_id'],
            ]
        );
    }

    public function removeSettingCategory($data)
    {
        $update_setting_sql = "UPDATE ".self::$dp."setting SET `deleted`=2 WHERE `setting_category_id` = :setting_category_id";

        self::$db->update($update_setting_sql, ['setting_category_id'=>$data['setting_category_id']]);

        $update_setting_category_sql = "UPDATE ".self::$dp."setting_category SET `deleted`=2 WHERE `setting_category_id` = :setting_category_id";

        return self::$db->update($update_setting_category_sql, ['setting_category_id'=>$data['setting_category_id']]);
    }

    public function removeSettingCategorys($data)
    {
        $update_setting_sql = "UPDATE ".self::$dp."setting SET `deleted`=2 WHERE `setting_category_id` = :setting_category_id";
        $update_setting_category_sql = "UPDATE ".self::$dp."setting_category SET `deleted`=2 WHERE `setting_category_id` = :setting_category_id";

        foreach ($data['setting_category_ids'] as $setting_category_id) {
            self::$db->update($update_setting_sql, ['setting_category_id'=>$setting_category_id]);
            self::$db->update($update_setting_category_sql, ['setting_category_id'=>$setting_category_id]);
        }

        return true;
    }
}
