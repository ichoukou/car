<?php
/**
 * 模型加载类
 */
namespace Libs\Core;

final class Model
{
    /**
     * Model类文件加载方法
     *
     * @param String $directory 根目录文件夹名称
     * @param Array $args[0] 文件夹名\\模板名称
     * @param Array $args[1] 方法名
     * @param Array $args[2] 参数
     *
     * @return Array 数据库返回值
     */
    public static function __callStatic($directory = null,$args = null)
    {
        list($model_directory,$model) = explode('\\',$args[0]);
        $directory = ucfirst($directory);
        $model_directory = ucfirst($model_directory);
        $model = ucfirst($model);

        $path = $directory.'\\Model\\'.$model_directory.'\\'.$model;

        if (class_exists($path)) {
            if (method_exists($path,$args[1])) {
                return call_user_func_array(array(new $path(),$args[1]),array($args[2]));
            } else {
                trigger_error("Error: The '{$args[1]}' Method Under Class '{$path}' Not Found!",E_USER_WARNING);
                return false;
            }
        } else {
            trigger_error("Error: Model '{$path}' Not Found!!",E_USER_WARNING);
            return false;
        }
    }
}