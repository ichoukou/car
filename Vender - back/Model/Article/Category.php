<?php
namespace Admin\Model\Article;

use Libs\Core\DbFactory AS DbFactory;

class Category extends DbFactory
{
    public function autocompleteFindArticleTypeSettings($data)
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

    public function findCategoryById($data)
    {
        $sql = "SELECT * FROM ".self::$dp."article_category WHERE `category_id` = :category_id";

        return self::$db->get_one($sql, ['category_id'=>$data['category_id']]);
    }

    public function findCategorys($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT * FROM ".self::$dp."article_category WHERE `deleted` = 1 ";

        if (!empty($params['filter_title'])) {
            $sql .= "AND `title` LIKE :title ";
            $conditions['title'] = "%".$params['filter_title']."%";
        }

        if (!empty($params['filter_article_type'])) {
            $sql .= "AND `article_type` = :article_type ";
            $conditions['article_type'] = $params['filter_article_type'];
        }

        if (!empty($params['filter_status'])) {
            $sql .= "AND `status` = :status ";
            $conditions['status'] = $params['filter_status'];
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY article_type ASC,category_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findCategorysCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."article_category WHERE `deleted` = 1 ";

        if (!empty($params['filter_title'])) {
            $sql .= "AND `title` LIKE :title ";
            $conditions['title'] = "%".$params['filter_title']."%";
        }

        if (!empty($params['filter_article_type'])) {
            $sql .= "AND `article_type` LIKE :article_type ";
            $conditions['article_type'] = $params['filter_article_type'];
        }

        if (!empty($params['filter_status'])) {
            $sql .= "AND `status` LIKE :status ";
            $conditions['status'] = $params['filter_status'];
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addCategory($data)
    {
        $sql = "INSERT INTO ".self::$dp."article_category (`title`,`desc`,`status`,`article_type`) VALUES ";

        return self::$db->insert(
            $sql,
            [
                $data['post']['title'],
                $data['post']['desc'],
                $data['post']['status'],
                $data['post']['article_type']
            ]
        );
    }

    public function editCategory($data)
    {
        $update_sql = "UPDATE " . self::$dp . "article_category SET"
            ." `title` = :title, `status` = :status, "
            ." `desc` = :desc, article_type = :article_type "
            ." WHERE `category_id` = :category_id";

        self::$db->update(
            $update_sql,
            [
                'title'                 => $data['post']['title'],
                'status'                => $data['post']['status'],
                'desc'                  => $data['post']['desc'],
                'article_type'          => $data['post']['article_type'],
                'category_id'           => $data['post']['category_id'],
            ]
        );
    }

    public function removeCategory($data)
    {
        $sql = "UPDATE ".self::$dp."article_category SET `deleted`=2 WHERE `category_id` = :category_id";

        return self::$db->update($sql, ['category_id'=>$data['category_id']]);
    }

    public function removeCategorys($data)
    {
        $sql = "UPDATE ".self::$dp."article_category SET `deleted`=2 WHERE `category_id` = :category_id";

        foreach ($data['category_ids'] as $category_id) {
            self::$db->update($sql, ['category_id'=>$category_id]);
        }

        return true;
    }
}
