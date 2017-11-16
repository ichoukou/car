<?php
/**
 * 数据库工厂模型类
 */
namespace Libs\Core;

use Libs\Core\Log as Log; #日志类

class DbFactory
{
    /**
     * @var Object $dp 数据库对象
     * @var String $dp 表前缀
     * @var Object $method 实例化的库包含的所有方法
     */
    protected static $db;
    public static $dp;
    public static $method;

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
            self::$db = new $cls($conf);
            self::$dp = $conf['db_prefix'];
            self::$method = get_class_methods(self::$db);
        } else {
            trigger_error("Error: Class '{$conf['db_type']}' Not Found! ", E_USER_WARNING);
            return false;
        }
    }
}