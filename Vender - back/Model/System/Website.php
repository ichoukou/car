<?php
namespace Vender\Model\System;

use Libs\Core\DbFactory AS DbFactory;

class Website extends DbFactory
{
    #此key对应这配置表里网站设置选项设置的选项的key
    private $key = 1;

    public function findWebsiteByWebsiteId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."website" .
            " WHERE `deleted` = 1 AND `website_id` = :website_id";

        $result = self::$db->get_one($sql, ['website_id'=>$data['website_id']]);

        return $result;
    }

    public function findWebsites($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT w.*,s.value FROM ".self::$dp."website AS w  " .
               " LEFT JOIN ".self::$dp."setting AS s ON w.status = s.setting_id " .
               " WHERE w.`deleted` = 1 ";

        $sql .= " ORDER BY w.website_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findWebsitesCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."website AS w " .
            " LEFT JOIN ".self::$dp."setting AS s ON w.status = s.setting_id " .
            " WHERE w.`deleted` = 1 ";

        return self::$db->count($sql, $conditions);
    }

    public function addWebsite($data)
    {
        /**
         * 如果此选项为启用,那么把其他信息设置为停用,此处的数字随setting表的setting_id来变
         */
        if ($data['post']['status'] == $this->key) {
            $update_sql = "UPDATE ".self::$dp."website SET `status` = 2 ";
            self::$db->update($update_sql);
        }

        $sql = "INSERT INTO ".self::$dp."website (`title`,`url`,`meta_title`,`meta_description`,`meta_keyword`,`website_logo`,`website_icon`,`status`) VALUES ";
        $website_id = self::$db->insert(
            $sql,
            [
                $data['post']['title'],
                $data['post']['url'],
                $data['post']['meta_title'],
                $data['post']['meta_description'],
                $data['post']['meta_keyword'],
                $data['post']['website_logo'],
                $data['post']['website_icon'],
                $data['post']['status']
            ]
        );

        return $website_id;
    }

    public function editWebsite($data)
    {
        if ($data['post']['status'] == $this->key) {
            $update_sql = "UPDATE ".self::$dp."website SET `status` = 2 ";
            self::$db->update($update_sql);
        }

        $update_sql = " UPDATE " . self::$dp . "website SET" .
                      " `title` = :title, `url` = :url, " .
                      " `meta_title` = :meta_title, `meta_description` = :meta_description, " .
                      " `meta_keyword` = :meta_keyword, `website_logo` = :website_logo, " .
                      " `website_icon` = :website_icon, `status` = :status " .
                      " WHERE `website_id` = :website_id";

        self::$db->update(
            $update_sql,
            [
                'website_id'           => $data['post']['website_id'],
                'title'                 => $data['post']['title'],
                'url'                   => $data['post']['url'],
                'meta_title'           => $data['post']['meta_title'],
                'meta_description'   => $data['post']['meta_description'],
                'meta_keyword'       => $data['post']['meta_keyword'],
                'website_logo'        => $data['post']['website_logo'],
                'website_icon'       => $data['post']['website_icon'],
                'status'             => $data['post']['status']
            ]
        );
    }

    public function removeWebsite($data)
    {
        $sql = "UPDATE ".self::$dp."website SET `deleted`=2 WHERE `website_id` = :website_id";

        return self::$db->update($sql, ['website_id'=>$data['website_id']]);
    }

    public function removeWebsites($data)
    {
        $sql = "UPDATE ".self::$dp."website SET `deleted`=2 WHERE `website_id` = :website_id";

        foreach ($data['website_ids'] as $website_id) {
            self::$db->update($sql, ['website_id'=>$website_id]);
        }

        return true;
    }

    public function findWebsiteByStatus($data)
    {
        $sql = "SELECT * FROM ".self::$dp."website " .
            " WHERE `deleted` = 1 AND `status` = {$this->key} LIMIT 1";

        $result = self::$db->get_one($sql);

        return $result;
    }

    /**
     * 方法里的user_type 对应这前后台配置文件session数组的user_type
     */
    public function findHeadInformationStatistics($data)
    {
        $sql = "SELECT * FROM ".self::$dp."session_info";
        $r = self::$db->get_all($sql);

        $result = [];
        if (!empty($r)) {
            foreach ($r as $k=>$v) {
                if ($v['uid'] > 0) {
                    $result[$v['user_type']]['is_login'] += 1;
                } else {
                    $result[$v['user_type']]['not_login'] += 1;
                }
            }
        }

        return $result;
    }
}
