<?php
/**
 * 路由控制器类
 */
namespace Libs\Core;

use Libs\ExtendsClass\Common AS ExtendsCommon;

class Route
{
    /**
     * 加载控制器
     *
     * @param Array $config 配置文件
     *
     * @return Null
     */
    public function controller($config, $entrance)
    {
        #如果没有路由参数，找配置文件对应的目录下的默认控制器，否则根据参数匹配控制器
        if (empty($_GET['route']) or strpos($_SERVER["QUERY_STRING"],'/') === false) {
            list($directory,$controller_directory,$controller) = explode('\\',$config['DEFAULT_ROUTE']['path']);

            $directory = ucfirst($directory);
            $controller_directory = ucfirst($controller_directory);
            $controller = ucfirst($controller);

            #$path = $directory.'\\Controller\\'.$controller_directory.'\\'.$controller;
            $path = $directory.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR.$controller_directory.DIRECTORY_SEPARATOR.$controller;
            $method = $config['DEFAULT_ROUTE']['method'];

            if (!is_file(ROOT_PATH.$path.'.php')) {
                trigger_error("Error: Default Class Not Found",E_USER_WARNING);
                return false;
            }

            $path = str_replace('/','\\',$path);
            $cls = new $path($config);

            if (!method_exists($cls,$method)) {
                trigger_error("Error: The '{$method}' Method Under Class '{$path}' Not Found!",E_USER_WARNING);
                return false;
            }

            $cls->$method();
        } else {
            $routes = ExtendsCommon::hsc($_GET['route']);

            list($directory,$controller_directory,$controller,$method) = explode('/',$routes);
            $directory = ucfirst($directory);
            $controller_directory = ucfirst($controller_directory);
            $controller = ucfirst($controller);
            $method = lcfirst($method) ?: $config['DEFAULT_ROUTE']['method'];

            if ($directory != $entrance) {
                var_dump('URL地址错误，正在处理，请稍等...<br>');
                exit(header("refresh:3;url={$entrance}.php"));
            }

            $path = $directory.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR.$controller_directory.DIRECTORY_SEPARATOR.$controller;
            if (is_file(ROOT_PATH.$path.'.php')) {
                $path = str_replace('/','\\',$path);
                $cls = new $path($config);

                if (!method_exists($cls,$method)) {
                    trigger_error("Error: The '{$method}' Method Under Class '{$path}' Not Found!",E_USER_WARNING);
                    return false;
                }

                $cls->$method();
            } else {
                var_dump('URL地址错误，正在处理，请稍等...<br>');
                exit(header("refresh:3;url={$config['DEFAULT_ROUTE']['entrance']}.php"));

                #trigger_error("Error: Class '{$path}' Not Found",E_USER_WARNING);
                #return false;
            }
        }
    }
}