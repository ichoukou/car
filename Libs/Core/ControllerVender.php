<?php
/**
 * 总控制器类
 */
namespace Libs\Core;

use Libs\Core\ValidateLogin;
use Libs\Core\WebsiteInfo;
use Libs\Core\Loader as L;

class ControllerVender
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
        'entrance' => 'vender.php?',
        'session_info'=> '',
        'vender_group'=>
            [
                1 => '超级管理员',
                2 => '管理员'
            ],
        'reservation_status' =>
            [
                1 => '已预约，未接待', #接待预约列表
                2 => '已接待，维修中，未结算', #另一个页面
                3 => '已结算，未支付', #用户可以支付
                4 => '已支付，未评价',
                5 => '已评价',
                6 => '订单交易异常'
            ]
    ];

    public function __construct($config)
    {
        $this->config = $config;
        $this->data['entrance'] = $this->config['DEFAULT_ROUTE']['entrance'];
        $this->data['session_info'] = $_SESSION;
        $website = new WebsiteInfo();
        $this->data['website_info'] = $website->get_vender_website_info();
        #$this->data['statistics'] = $website->get_vender_head_information_statistics();
    }

    /**
     * 验证登陆
     */
    public function is_login()
    {
        $validate_login = new ValidateLogin();

        $bool = $validate_login->is_company_login($this->config['session']);

        if (!$bool) {
            if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                setcookie('error_info', '登陆超时,请登陆', time() + 60);

                exit(header("location:{$this->data['entrance']}route=Vender/Account/Account/login"));
            } else {
                $errors ['other_error'] = '登陆超时,请登陆';
                exit(json_decode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * 引入header、footer等页面
     */
    public function create_page()
    {
        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            $this->data['header_page'] = L::view('Common\\Header', 'Vender', $this->data);

            #$this->data['left_page'] = L::view('Common\\Left', 'Vender', $this->data);

            $this->data['footer_page'] = L::view('Common\\Footer', 'Vender', $this->data);
        }
    }
}