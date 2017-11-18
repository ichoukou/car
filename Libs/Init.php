<?php
/**
 * 初始化类
 */
use Libs\Core\Route;
use Libs\Core\Alias;
use Libs\Core\DbFactory;
use Libs\Core\Loader;
use Libs\Core\Log;
use Libs\Core\Session;

final class init
{
    #初始化
    public function __construct($_config, $entrance)
    {
        #错误处理
        set_error_handler(array($this,'_error_handler'));

        #自动加载
        spl_autoload_register(array($this,'_autoload'));
        spl_autoload_extensions('.php');

        #log类配置文件
        Log::$conf = $_config['log'];

        #初始化数据库工厂
        DbFactory::connect($_config['DB']['true']);

        #初始化Session类
        Session::connect($_config['session']);

        #实例化加载模型
        #new Loader();

        #类别名
        #new Alias();

        #路由器
        $route = new Route();
        $route->controller($_config, $entrance);
    }

    #自动加载
    public function _autoload($class)
    {
        $file = ROOT_PATH . str_replace('\\','/',$class) . '.php';

        if (is_file($file)) {
            require_once($file);
        } else {
            trigger_error("Error: Class '{$file}' Not Found! " ,E_USER_WARNING);
        }
    }

    #错误处理
    public function _error_handler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() === 0) return false;

        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
                $error = 'Notice';
                break;
            case E_WARNING:
            case E_USER_WARNING:
                $error = 'Warning';
                break;
            case E_ERROR:
            case E_USER_ERROR:
                $error = 'Fatal Error';
                break;
            default:
                $error = 'Unknown';
                break;
        }

        if ($error != 'Notice') {
            Log::wirte_other_log('<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>');
            echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
        }

        if ($error == 'Notice') {
            #Log::wirte_other_log('<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>');
            #exit(trigger_error("Error: {$message}", E_USER_ERROR));
        }

        return true;
    }
}