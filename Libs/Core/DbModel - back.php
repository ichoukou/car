<?php
/**
 * 数据库模型类
 */
namespace Libs\Core;

use Libs\Core\DbFactory AS DbFactory;

class DbModel extends DbFactory
{
    /**
     * 通用方法
     *
     * @param String $sql sql语句
     *
     * @return Array 数据库返回值
     */
    public static function query($sql)
    {
        return parent::query($sql);
    }

    /**
     * 获取多条数据
     *
     * @param String $sql sql语句
     *
     * @return Array 数据库返回值
     */
    public static function get_all($sql)
    {
        $result = parent::query($sql);
        return $result['rows'];
    }

    /**
     * 获取单条数据
     *
     * @param String $sql sql语句
     *
     * @return Array 数据库返回值
     */
    public static function get_one($sql)
    {
        $result = parent::query($sql);
        return $result['row'];
    }

    /**
     * 插入数据
     *
     * @param String $sql sql语句
     *
     * @return Array array('bool'=>'true/false','last_id')
     */
    public static function insert($sql)
    {
        return parent::insert($sql);
    }

    /**
     * 更新数据
     *
     * @param String $sql sql语句
     *
     * @return Bool true/false
     */
    public static function update($sql)
    {
        return parent::update($sql);
    }

    /**
     * 统计数据
     *
     * @param String $sql sql语句
     *
     * @return Array array('count'=>?)
     */
    public static function count($sql)
    {
        return parent::count($sql);
    }

    /**
     * 过滤数据
     *
     * @param String $sql sql语句
     *
     * @return String 返回值
     */
    public static function escape($sql)
    {
        return parent::escape($sql);
    }

    /**
     * 返回前一次 MySQLi 操作所影响的记录行数
     *
     * @return Number 返回值
     */
    public static function count_affected()
    {
        return parent::countAffected();
    }

    /**
     * 返回前一次 MySQLi 数据操作的自增Id
     *
     * @return Number 返回值
     */
    public static function get_last_id()
    {
        return parent::getLastId();
    }

    /*
     *  调用mysqli的real_escape_string()数据过滤参数方法
     *  @param $data 数组参数 必须是数组才有返回值 如果数组里面嵌套二维数组 下面有判断自动处理成in()来过滤
     *  @param $separator 组成新语句的分隔符
     *  @like like语句查询判断条件 如果$key在这个数组里面 默认匹配成like查询 传参参数写法 -> array('like1','like2','like3',...)
     *  return 返回sql语句
     * */
    public static function mres_filter_data($data = null,$separator = ',',$like = array())
    {
        if (empty($data) or !is_array($data)) return '';

        $sql = '';
        $num = 1;
        foreach ($data as $key=>$val) {
            if (isset($val)) {
                $sql .= ($num != 1) ? " {$separator} " : ' ';

                #sql语句 in
                if (is_array($val)) {
                    $arr = implode(',',$val);
                    foreach ($arr as $k=>$v) {
                        $arr[$k] = parent::escape($v);
                    }
                    $sql .= "`{$key}` IN (".$arr.")";
                    $num++;
                    continue;
                }

                #sql语句 like 和 =
                if (!empty($like) and in_array($key,$like)) {
                    $sql .= "`{$key}` like '%" . parent::escape($val) . "%'";
                } else {
                    $sql .= "`{$key}`='" . parent::escape($val) . "'";
                }

                $num++;
            }
        }

        return $sql;
    }
}