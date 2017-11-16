<?php
/**
 * 总控制器类
 */
namespace Libs\Core;

use Libs\Core\ValidateLogin;
use Libs\Core\Loader as L;

class Controller
{
    /**
     * 配置文件
     *
     * @var Array $config
     */
    protected $config;

    /**
     * 验证登录
     *
     * @var Bool True/False
     */
    protected $validate_login;

    /**
     * 参数
     *
     * @var Array $data
     */
    protected $data = [
        'assets_server'  => ASSETS_SERVER,
        'http_server'    => HTTP_SERVER,
        'themes_default' => 'Default',
        'entrance' => 'index.php?'
    ];

    public function __construct($config)
    {
        $this->config = $config;
        $this->data['entrance'] = $this->config['DEFAULT_ROUTE']['entrance'];

        #非ajax请求,只适用于jquery请求,如js请求,请自行添加header信息
        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            $this->data['header_page'] = L::view('Common\\Header', 'Front', $this->data);

            $this->data['footer_page'] = L::view('Common\\Footer', 'Front', $this->data);
        }
    }

    /**
     * 验证登陆
     */
    public function is_login()
    {
        $validate_login = new ValidateLogin();

        $bool = $validate_login->is_login($this->config['session']);

        if (!$bool) {
            exit(header('location:index.php?route=Front/Account/Account/login'));
        }
    }
}