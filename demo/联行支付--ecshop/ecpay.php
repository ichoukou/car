<?php
global $_LANG;
$_LANG['ecpay']      = '联行支付平台';
$_LANG['ecpay_desc'] = '山东省唯一的基础性电子商务综合服务平台，是有中国特色、代表山东形象的电子商务门户，在全省未来经济发展和社会生活的宏伟蓝图中起纽带作用；国内首次将电子商务服务与网上社区服务相统一, 首个与银联共建支付网关的省级电子商务综合服务平台。';
$_LANG['ecpay_merId'] = '商户身份ID';
$_LANG['ecpay_key'] = '商户授权码';
/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE){
    $i = isset($modules) ? count($modules) : 0;
    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');
    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'ecpay_desc';
    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';
    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';
    /* 作者 */
    $modules[$i]['author']  = 'ECPAY';
    /* 网址 */
    $modules[$i]['website'] = 'http://www.ecpay.cn';
    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';
    /* 配置信息 */
    $modules[$i]['config']  = array(
     	array('name' => 'ecpay_merId',           'type' => 'text',   'value' => ''),
        array('name' => 'ecpay_key',               'type' => 'text',   'value' => ''),
    );
    return;
}

/**
 * 类
 */
class ecpay{

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function ecpay(){
    }

    function __construct(){
        $this->ecpay();
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment){
    	//商户代码（merId）
    	$merId = $payment['ecpay_merId'];
    	//商户系统生成的订单号
		$random =
		$dealOrder = rand(0,1000000000);
    	//支付金额，保留两个小数位
    	$dealFee	= number_format($order['order_amount'],2);;
    	//订单支付结果同步返回地址
		$dealReturn = return_url(basename(__FILE__, '.php'));	
		//订单支付结果异步返回地址
		$dealNotify = return_url(basename(__FILE__, '.php'));
		//生成签名
		$dealSignure=sha1($merId.$dealOrder.$dealFee.$dealReturn.$payment['ecpay_key']);

		//获得表单传过来的数据
        $def_url  = '<br />';
        $def_url  = '<form method="post" action="http://user.sdecpay.com/paygate.html"  >';
        $def_url .= '	<input type = "hidden" name = "merId"	value = "'.$merId.'">';
        $def_url .= '	<input type = "hidden" name = "dealOrder" 				value = "'.$dealOrder.'">';
        $def_url .= '	<input type = "hidden" name = "dealFee" 			value = "'.$dealFee.'">';
        $def_url .= '	<input type = "hidden" name = "dealSignure"			value = "'.$dealSignure.'">';
        $def_url .= '	<input type = "hidden" name = "dealReturn"			value = "'.$dealReturn.'">';
		$def_url .= '	<input type = "hidden" name = "dealNotify"			value = "'.$dealNotify.'">';
        $def_url .= '	<input type=submit value="立即付款">';
        $def_url .= '</form>';
        return $def_url;
    }

    /**
     * 响应操作
     */
    function respond(){
        $payment = get_payment(basename(__FILE__, '.php'));
		$dealOrder = $_REQUEST['dealOrder'];
		$dealFee = $_REQUEST['dealFee'];
		$dealState = $_REQUEST['dealState'];
		$dealSignature = $_REQUEST['dealSignature'];
		$dealId = $_REQUEST['dealId'];
		//生成签名
		$strSignature = sha1($dealOrder.$dealState.$payment['ecpay_key']);
		if ( $dealSignature !=$strSignature){
			return false;
		}else{
			if(isset($dealSignature)){
				if($dealSignature=="1"){
					order_paid($dealOrder);
					return true;
				}else{
     				return false;
				}
			}
		}
    }
}
?>