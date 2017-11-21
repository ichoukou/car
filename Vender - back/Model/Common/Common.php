<?php
namespace Vender\Model\Common;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common AS ExtendsCommon;

class Common extends DbFactory
{
    /**
     * @param 如果data['return_data_format'] 为空,返回的数据格式为数组,如果 = kv或者其他,返回键值对
     * @return string
     */
    public function getSettings($data)
    {
        if (is_array($data['module'])) {
            if (count($data['module']) > 0) {
                $likes = '';
                foreach ($data['module'] as $module) {
                    if (!empty($likes)) $likes .= ',';
                    $likes .= self::$db->quote($module);
                }

                $sql = "SELECT * FROM ".self::$dp."setting_category WHERE `module` IN ({$likes}) AND `deleted` = 1";

                $setting_category = self::$db->get_all($sql);
            } else {
                $sql = "SELECT * FROM ".self::$dp."setting_category AND `deleted` = 1";

                $setting_category = self::$db->get_all($sql);
            }
        } else {
            if (!empty($data['module'])) {
                $sql = "SELECT * FROM ".self::$dp."setting_category WHERE `module` = :module AND `deleted` = 1";

                $setting_category = self::$db->get_all($sql, ['module'=>$data['module']]);
            } else {
                $sql = "SELECT * FROM ".self::$dp."setting_category AND `deleted` = 1";

                $setting_category = self::$db->get_all($sql);
            }
        }

        $result = [];
        if (!empty($setting_category)) {
            foreach ($setting_category as $setting_category_id=>$category) {
                $setting_sql = "SELECT * FROM ".self::$dp."setting WHERE `setting_category_id` = :setting_category_id AND `deleted` = 1 ORDER BY sort DESC ";

                $settings = self::$db->get_all($setting_sql, ['setting_category_id'=>$category['setting_category_id']]);

                if (!empty($settings)) {
                    $result[$category['key']]['title'] = $category['title'];
                    $result[$category['key']]['infos'] = $settings;
                }
            }
        }

        return $result;
    }

    public function getConfigs($data)
    {
        $config_sql = "SELECT * FROM ".self::$dp."config WHERE `module` = :module";

        $configs = self::$db->get_all($config_sql, ['module'=>$data['module']]);

        $result = [];
        if (!empty($configs)) {
            foreach ($configs as $config) {
                $result[$config['key']] = $config;
            }
        }

        return $result;
    }
}
