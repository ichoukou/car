<?php
/**
 * 数据库工厂模型类
 */
namespace Libs\Core;

class DbFactory
{
    /**
     * @var Object $dp 数据库对象
     * @var String $dp 表前缀
     */
    protected static $db;
    public static $dp;

    /**
     * 实例化数据库
     *
     * @param Array $conf 配置文件
     * @return Bool true/false
     */
    public static function connect($conf)
    {
        $cls = 'Libs\\Db\\'.$conf['db_type'];

        if (class_exists($cls)) {
            self::$db = new $cls($conf['db_hostname'], $conf['db_username'], $conf['db_password'], $conf['db_database'],$conf['db_port']);
            self::$dp = $conf['db_prefix'];
        } else {
            trigger_error("Error: Class '{$conf['db_type']}' Not Found! " ,E_USER_WARNING);
            return false;
        }
    }

    protected static function query($sql)
    {
        return self::$db->query($sql);
    }

    protected static function count($sql)
    {
        return self::$db->count($sql);
    }

    protected static function insert($sql)
    {
        return array(
            'bool' => self::$db->query($sql),
            'last_id' => self::$db->get_last_id()
        );
    }

    protected static function update($sql)
    {
        return self::$db->query($sql);
    }

    protected static function escape($value)
    {
        return self::$db->escape($value);
    }

    protected static function count_affected()
    {
        return self::$db->count_affected();
    }

    protected static function get_last_id()
    {
        return self::$db->get_last_id();
    }
}