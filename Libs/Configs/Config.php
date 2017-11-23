<?php
error_reporting(E_ALL ^ E_NOTICE);
header("Content-type: text/html; charset=utf-8");

#验证微信登陆
#if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'micromessenger') === false)
#    exit('<span style="font-size:28px">HTTP/1.1 401 Unauthorized</span>');

#定义常量
define('HTTP_SERVER', "http://{$_SERVER['HTTP_HOST']}/");
#define('HTTP_SERVER', "http://{$_SERVER['HTTP_HOST']}/test/");#线上测试
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
define('ASSETS_SERVER', HTTP_SERVER."Libs" . DIRECTORY_SEPARATOR."Assets" . DIRECTORY_SEPARATOR);
define('DIR_IMAGE', ROOT_PATH."Image" . DIRECTORY_SEPARATOR);
define('URL_IMAGE', HTTP_SERVER . 'Image' . DIRECTORY_SEPARATOR);
define('URL_IMAGE_CACHE', HTTP_SERVER . 'Image' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR);
define('URL_IMAGE_CACHE_WITH', 100);
define('URL_IMAGE_CACHE_HEIGHT', 100);
define('NO_IMAGE', HTTP_SERVER . 'image/cache/no_image-100x100.png');

$_config = [];

$_config['DEFAULT_ROUTE'] = [
    'path'   => 'Front\Account\Account',
    'method' => 'index',
    'entrance' => 'index.php?'
];

#数据库配置文件,key为true代表数据库启用,同时只能启用一个
$_config['DB'] = [
    'true' => [
        'db_type'     => 'PdoMySQL',
        'db_host'     => '127.0.0.1',
        'db_name'     => 'car',
        'db_username' => 'root',
        'db_password' => 'root',
        'db_port'     => '3306',
        'db_prefix'   => 'website_',
    ],
    'false' => [
        'db_type'     => 'MySQLi',
        'db_host'     => '127.0.0.1',
        'db_name'     => 'bb',
        'db_username' => 'root',
        'db_password' => 'root',
        'db_port'     => '3306',
        'db_prefix'   => 'website_',
    ]
];

#微信相关配置
$_config['WX_CONF'] = [
    'appid'     => 'wxxxxxxxx',
    'appsecret' => 'f47xxxxxxxxxxxxx',
    'mchid'       => ''#商户id
];

$_config['UPDIR'] = [
    'path' => 'upload/'
];

$_config['log'] = [
    'error_db_log_path' => ROOT_PATH.'Libs'.DIRECTORY_SEPARATOR.'Log'.DIRECTORY_SEPARATOR.'DbError.log',
    'error_other_log_path' => ROOT_PATH.'Libs'.DIRECTORY_SEPARATOR.'Log'.DIRECTORY_SEPARATOR.'OtherLog.log',
];

/**
 * session配置
 *
 * save_type: session存储类型,目前支持　1、原生存储方式(请修改配置文件　session.save_handler = Files) 2、数据库存储方式(请修改配置文件　session.save_handler = User)
 * expiration_time: session过期时间,单位秒
 * session_name: 保存在客户端cookie的名称,如果是默认的存储方式,必须与php.ini配置文件的session.name相同
 * user_type: 用于判断用户是前台登录还是后台登录
 */
$_config['session'] = [
    'save_type'       => 1,
    'expiration_time' => 1800,
    'session_name'    => 'MY_USER_PHPSESSID',
    'user_type'       => 'user',
];
