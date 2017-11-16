<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/9
 * Time: 10:29
 */

$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
if(strpos($useragent, 'micromessenger') === false){
//    echo ('<span style="font-size:28px">HTTP/1.1 401 Unauthorized</span>');
//    exit;
}

error_reporting(E_ALL ^ E_NOTICE);

header("Content-type: text/html; charset=utf-8");

#session_start();

if (!session_id()) {
    ini_set('session.use_only_cookies', 'On');
    ini_set('session.use_trans_sid', 'Off');
    ini_set('session.cookie_httponly', 'On');

    session_set_cookie_params(0, '/');
    session_start();
}

//DB
define('DB_HOSTNAME', 'rdsoo1yu51er33gz6bkrj.mysql.rds.aliyuncs.com');
define('DB_USERNAME', 'wechatonline');
define('DB_PASSWORD', 'wechatonline123');
define('DB_DATABASE', 'huodongdb');
define('DB_PREFIX', 'qingpi_2017_beer_');

//微信
define('APPID', 'wx7ad3f7da78cbafc1');
define('APPSECRET', '8e374858a924c808d370632d7e3294fd');

define('SHARE_APPID', 'wx5c7015f957c13362');
define('SHARE_APPSECRET', '3a1b37f78e26636dbb2c996b2f16f1de');

define('OSS_URL', 'http://hdfile.wechatdpr.com/qingpi/2017/beer/');
define('HTTP_URL', 'http://hd.wechatdpr.com/qingpi/2017/beer/');
define('OSS_ROOT', 'qingpi/2017/beer');

define('TIMESTAMP',time());

define('DB_TOKEN_ID','3');
define('DB_TICKET_ID','15');
define('DB_CARD_TICKET_ID','1');

define('DB_SHARE_TOKEN_ID','6');
define('DB_SHARE_TICKET_ID','25');

define('UPDIR','upload/');

define ( 'MCHID', "10057495"); //商户id

define ( 'PARTNERKEY', "F97C8F39472C1B17D5AE3BF04DD5C8B6" ); //秘钥

define('ROOT_PATH', dirname(dirname(__FILE__)));
define('DS', DIRECTORY_SEPARATOR);

$admin_group =
    [
        [
            'group_id' => 1,
            'group_name' => '超级管理员'
        ],
        [
            'group_id' => 2,
            'group_name' => '高级管理员'
        ],
        [
            'group_id' => 3,
            'group_name' => '普通管理员'
        ]
    ];

$option_types =
    [
        1 => '优点',
        2 => '缺点'
    ];

$group_types =
    [
        1 => '初赛',
        2 => '决赛'
    ];

$channels =
    [
        1 => '消费者',
        2 => '经销商',
        3 => '专业',
        4 => '员工',
        5 => '领导',
    ];