<?php
namespace Admin\Model\Article;

use Libs\Core\DbFactory AS DbFactory;

class News extends DbFactory
{
    #此key对应这配置表里文章模块选项的key
    private $key = 2;

    public function autocompleteFindCategory($data)
    {
        $sql = "SELECT ac.category_id,ac.title,s.value AS setting_title FROM ".self::$dp."article_category AS ac " .
            " LEFT JOIN ".self::$dp."setting AS s ON ac.article_type = s.setting_id " .
            #" LEFT JOIN ".self::$dp."setting_category AS sc ON sc.setting_category_id = s.setting_category_id " .
            " WHERE ac.`deleted` = 1 AND s.key = {$this->key} ";

        if (!empty($data['post']['filter_param'])) {
            $sql .= " AND ac.`title` LIKE :title ";
            $conditions['title'] = "%".$data['post']['filter_param']."%";
        }

        $return = self::$db->get_all($sql, $conditions);
        $result = [];
        if (!empty($return)) {
            foreach ($return as $key=>$category) {
                $result[$category['category_id']] = $category;
            }
        }

        return $result;
    }

    public function findNewsById($data)
    {
        $sql = "SELECT * FROM ".self::$dp."article_news WHERE `news_id` = :news_id AND `deleted` = 1 ";
        $result = self::$db->get_one($sql, ['news_id'=>$data['news_id']]);

        $find_category_sql = "SELECT anc.*,ac.title as category_title,s.value AS setting_title FROM ".self::$dp."article_news_category AS anc " .
            " LEFT JOIN ".self::$dp."article_category AS ac ON anc.category_id = ac.category_id " .
            " LEFT JOIN ".self::$dp."setting AS s ON ac.article_type = s.setting_id " .
            " WHERE anc.`article_id` = :news_id AND ac.deleted = 1 ";
        $result['category_ids'] = self::$db->get_all($find_category_sql, ['news_id'=>$data['news_id']]);

        if (!empty($result)) {
            $find_image_sql = "SELECT * FROM ".self::$dp."article_news_images WHERE `news_id` = :news_id";
            $result['other_images'] = self::$db->get_all($find_image_sql, ['news_id'=>$result['news_id']]);
        }

        return $result;
    }

    public function findNewss($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT an.* FROM ".self::$dp."article_news AS an " .
            " WHERE an.`deleted` = 1 ";

        if (!empty($params['filter_title'])) {
            $sql .= " AND an.`title` LIKE :title ";
            $conditions['title'] = "%".$params['filter_title']."%";
        }

        if (!empty($params['filter_status'])) {
            $sql .= " AND an.`status` LIKE :status ";
            $conditions['status'] = $params['filter_status'];
        }

        if (!empty($params['filter_category'])) {
            $sql .= " AND EXISTS (SELECT anc.* FROM ".self::$dp."article_news_category AS anc WHERE anc.category_id = :category_id AND an.news_id = anc.article_id) ";
            $conditions['category_id'] = $params['filter_category'];
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY an.news_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        if (!empty($result)) {
            foreach ($result as $ck=>$cv) {
                $find_category_sql = "SELECT * FROM ".self::$dp."article_news_category WHERE `article_id` = :news_id ";
                $result[$ck]['category_ids'] = self::$db->get_all($find_category_sql, ['news_id'=>$cv['news_id']]);
            }
        }

        return $result;
    }

    public function findNewssCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."article_news AS an " .
            " WHERE an.`deleted` = 1 ";

        if (!empty($params['filter_title'])) {
            $sql .= " AND an.`title` LIKE :title ";
            $conditions['title'] = "%".$params['filter_title']."%";
        }

        if (!empty($params['filter_status'])) {
            $sql .= " AND an.`status` LIKE :status ";
            $conditions['status'] = $params['filter_status'];
        }

        if (!empty($params['filter_category'])) {
            $sql .= " AND EXISTS (SELECT anc.* FROM ".self::$dp."article_news_category AS anc WHERE anc.category_id = :category_id AND an.news_id = anc.article_id) ";
            $conditions['category_id'] = $params['filter_category'];
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addNews($data)
    {
        $sql = "INSERT INTO ".self::$dp."article_news (`title`,`content`,`status`,`image_path`) VALUES ";
        $news_id = self::$db->insert(
            $sql,
            [
                $data['post']['title'],
                $data['post']['content'],
                $data['post']['status'],
                $data['post']['image_path']
            ]
        );

        #添加关联表数据
        if (!empty($data['post']['category_id'])) {
            $insert_association_table_sql = "INSERT INTO ".self::$dp."article_news_category (`category_id`,`article_id`) VALUES ";
            $category_ids = [];
            foreach ($data['post']['category_id'] as $arr_key=>$category_id) {
                if (!empty($category_id)) {
                    $category_ids[$arr_key][0] = $category_id;
                    $category_ids[$arr_key][1] = $news_id;
                }
            }

            if (!empty($category_ids))
                self::$db->insert($insert_association_table_sql, $category_ids);
        }

        #添加图片
        if (!empty($data['post']['other_image_path'])) {
            $insert_image_sql = "INSERT INTO ".self::$dp."article_news_images (`news_id`,`image_path`,`sort`) VALUES ";
            $other_image_path = [];
            foreach ($data['post']['other_image_path'] as $other_image_path_key=>$other_image_path_value) {
                if (!empty($other_image_path_value['image_path'])) {
                    $other_image_path[$other_image_path_key][0] = $news_id;
                    $other_image_path[$other_image_path_key][1] = $other_image_path_value['image_path'];
                    $other_image_path[$other_image_path_key][2] = $other_image_path_value['sort'];
                }
            }

            if (!empty($other_image_path))
                self::$db->insert($insert_image_sql, $other_image_path);
        }

        return $news_id;
    }

    public function editNews($data)
    {
        $update_sql = "UPDATE " . self::$dp . "article_news SET"
            ." `title` = :title, `status` = :status, "
            ." `content` = :content, `image_path` = :image_path "
            ." WHERE `news_id` = :news_id";

        self::$db->update(
            $update_sql,
            [
                'title'                 => $data['post']['title'],
                'status'                => $data['post']['status'],
                'content'               => $data['post']['content'],
                'image_path'            => $data['post']['image_path'],
                'news_id'               => $data['post']['news_id'],
            ]
        );

        $new_images = [];
        if (!empty($data['post']['other_image_path'])) {
            $image_ids = '';
            foreach ($data['post']['other_image_path'] as $post_key=>$image) {
                #如果news_image_id不为空，说明是已存在的数据
                if (!empty($image['news_image_id'])) {
                    if (!empty($image_ids))
                        $image_ids .= ',';

                    $image_ids .= (int)$image['news_image_id'];

                    #更新已存在的图片数据
                    self::$db->update(
                        "UPDATE ".self::$dp."article_news_images SET `sort` = :sort, `image_path` = :image_path WHERE `news_image_id` = :news_image_id AND `news_id` = :news_id ",
                        [
                            'news_image_id'     => $image['news_image_id'],
                            'news_id'           => $data['post']['news_id'],
                            'sort'              => $image['sort'],
                            'image_path'        => $image['image_path']
                        ]
                    );
                } elseif(!empty($image['image_path'])) {
                    #整理需要存入的新图片数据
                    $new_images[$post_key][0] = $data['post']['news_id'];
                    $new_images[$post_key][1] = $image['image_path'];
                    $new_images[$post_key][2] = $image['sort'];
                }
            }

            #如果存在旧的数据，需要筛选出哪些旧的数据被删除，单独从数据库删除这些数据
            if (!empty($image_ids)) {
                $find_not_exists_image_sql = "SELECT * FROM ".self::$dp."article_news_images WHERE `news_id` = {$data['post']['news_id']} AND `news_image_id` NOT IN ({$image_ids}) ";
                $find_not_exists_images = self::$db->query($find_not_exists_image_sql);
                if (!empty($find_not_exists_images)) {
                    foreach ($find_not_exists_images as $need_remove) {
                        self::$db->delete(
                            "DELETE FROM ".self::$dp."article_news_images WHERE `news_image_id` = :news_image_id",
                            ['news_image_id' => $need_remove['news_image_id']]
                        );
                    }
                }
            }

            #存入新图片数据
            if (!empty($new_images)) {
                self::$db->insert(
                    "INSERT INTO ".self::$dp."article_news_images (`news_id`,`image_path`,`sort`) VALUES ",
                    $new_images
                );
            }
        } else {
            self::$db->delete(
                "DELETE FROM ".self::$dp."news_images WHERE `news_id` = :news_id",
                ['news_id' => $data['post']['news_id']]
            );
        }

        // -------------------------------------------

        if (!empty($data['post']['category_id'])) {
            $post_category_ids = '';
            $post_category_ids_arr = [];
            foreach ($data['post']['category_id'] as $arr_key=>$category_id) {
                if(!empty($category_id)) {
                    if (!empty($post_category_ids))
                        $post_category_ids .= ',';

                    $post_category_ids .= (int)$category_id;
                    array_push($post_category_ids_arr, (int)$category_id);
                }
            }

            #找出在新数据中在数据库存在的原数据
            if (!empty($post_category_ids)) {
                $find_exists_sql = "SELECT * FROM ".self::$dp."article_news_category WHERE `article_id` = {$data['post']['news_id']} AND `category_id` IN ({$post_category_ids}) ";
                $find_exists = self::$db->query($find_exists_sql);

                if (!empty($find_exists)) {
                    foreach ($find_exists as $exists_key=>$exists_value) {
                        #如果已存在的数据在新提交的数组中，则不需要重新处理，直接从$post_category_ids_arr里删除，剩下的就是需要新增的
                         if (in_array($exists_value['category_id'], $post_category_ids_arr)) {
                             $keys = array_keys($post_category_ids_arr, $exists_value['category_id']);
                             unset($post_category_ids_arr[$keys[0]]);
                         }
                    }
                }

                #如果数据库中，此文章对应的原数据不存在新提交的栏目数组中，说明已经被删除，清除数据库中的数据
                $remove_not_exists_sql = "DELETE FROM ".self::$dp."article_news_category WHERE `article_id` = {$data['post']['news_id']} AND `category_id` NOT IN ({$post_category_ids}) ";
                self::$db->execute($remove_not_exists_sql);

                #如果处理过数据后的数据不为空，新增数据
                if (!empty($post_category_ids_arr)) {
                    $new_category_ids = [];
                    foreach ($post_category_ids_arr as $ckey=>$cid) {
                        $new_category_ids[$ckey][0] = $data['post']['news_id'];
                        $new_category_ids[$ckey][1] = $cid;
                    }

                    self::$db->insert(
                        "INSERT INTO ".self::$dp."article_news_category (`article_id`,`category_id`) VALUES ",
                        $new_category_ids
                    );
                }
            }
        }
    }

    public function removeNews($data)
    {
        $sql = "UPDATE ".self::$dp."article_news SET `deleted`=2 WHERE `news_id` = :news_id";

        return self::$db->update($sql, ['news_id'=>$data['news_id']]);
    }

    public function removeNewss($data)
    {
        $sql = "UPDATE ".self::$dp."article_news SET `deleted`=2 WHERE `news_id` = :news_id";

        foreach ($data['news_ids'] as $news_id) {
            self::$db->update($sql, ['news_id'=>$news_id]);
        }

        return true;
    }
}
