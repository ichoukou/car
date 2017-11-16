<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/16
 * Time: 9:47
 */

/* 授权获取个人信息的方法 */

/**
 *  微信auto2授权获得用户资料第一步 根据appid,redirect_uri,scope,state获得code
 *  @param $appid 认证过的微信公众号的appid
 *  @param $redirect_uri 接收回调code的页面 这个页面要确保微信可以访问到
 *  @param $scope 授权方式 1.snsapi_base 静默授权 不弹出授权页面 默认获得权限 只能拿到openid. 2.snsapi_userinfo 弹出授权页面 可以获取到用户已填的信息 在未关注的情况下 只要用户授权 也可以拿到用户信息
 *  @param $state 任意输入一个值
 *  @return header跳转到redirect_uri 获得返回值code
 * */
function get_auto_code($appid = null,$redirect_uri = null,$scope = "snsapi_userinfo",$state = 0){
    if(empty($appid) || empty($redirect_uri)) return false;
    $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
    header('location:' . $url);
    exit;
}

/**
 *  微信auto2授权获得用户资料第二步 根据code,appid,secret获得access_token和openid
 *  @param $code 第一步时返回的code
 *  @param $appid 认证过的微信公众号的appid
 *  @param $secret 认证过的微信公众号的secret
 *  @return 通过请求返回带有access_token和openid的数组
 * */
function get_token($code = null,$appid = null,$appsecret = null){
    if(empty($code) || empty($appid) || empty($appsecret)) return false;

    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' .$code . '&grant_type=authorization_code';
    $token_arr = http_request($url);

    if (isset ( $token_arr ['access_token'] )) {
        return $token_arr;
    }

    return false;
}

/**
 *  微信auto2授权获得用户资料第三步 根据access_token,openid获得用户信息
 *  @param $access_token 第二步时返回的access_token
 *  @param $openid 第二步时返回的openid
 *  @return 通过请求返回用户信息数组
 * */
function get_wxuserinfo($access_token = null, $openid = null) {
    if (empty ( $access_token ) || empty ( $openid )) return false;

    $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
    $info_arr = http_request($url);

    if (isset ( $info_arr ['openid'] )) {
        // set_dbtoken ( $token_obj->access_token );$now = time ();
        //setcookie ( 'openid', $token_arr ['openid'], time() + 3600 * 24 * 365 );
        //setcookie ( 'openid', $token_arr ['openid']);
        return $info_arr;
    }
    return false;
}

function http_request($url = null,$data = null,$status = 0){
    if(empty($url)) return false;

    $curl = curl_init ();
    curl_setopt ( $curl, CURLOPT_URL, $url );
    curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );

    $result_json = curl_exec ( $curl );
    curl_close ( $curl );
    var_dump($result_json);
    if($status == 0){
        $result_json = json_decode ( $result_json, TRUE );
    }

    if(!empty($result_json)){
        return $result_json;
    }

    return false;
}

/* 调用WX接口 获取分享等接口的方法 */

/**
 *	分享接口第一步 通过appid和appsecret获取access_token
 *  @param $appid 认证过的微信公众号的appid
 *  @param $appsecret 认证过的微信公众号的secret
 *	@return 带有access_token的数组
 */
function wx_get_token($appid = null,$appsecret = null){
    if(empty($appid) || empty($appsecret)) return false;

    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
    $token_info = http_request($url);

    if(!empty($token_info['access_token'])){
        return $token_info;
    }

    return false;
}

/**
 *	分享接口第二步 通过access_token获取ticket
 *	@param access_token 第一步里获取到的
 *  @return 带有ticket的数组
 */
function wx_get_jsapi_ticket($access_token = null,$type = 'jsapi'){
    if(empty($access_token)) return '获取ticket的access_token为空。';

    $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type={$type}";

    $ticket_info = http_request($url);

    if(!empty($ticket_info['ticket'])){
        return $ticket_info;
    }

    return false;
}

/**
 *	分享接口第三步 通过ticket、noncestr、timestamp、分享的url生成签名
 *	@param ticket是第二步里获取到的,noncestr是一个任意字符串,timestamp是时间戳,url是你要分享给别人的url 从http或https开始 一般就是你自己的某个网站页面,如果有参数
 *          按照get请求的参数加上
 *  @result 这个生成前面的结果值可以在之前填写的url做安全验证
 *  @return 直接返回json_encode处理好的参数 在页面上的wx.config里使用
 */
function create_signature($ticket = null,$signature_url = null,$nonceStr = "mcl"){
    if(empty($ticket) || empty($signature_url)) return false;
    $ticket = $ticket;
    $nonceStr = $nonceStr;
    $timestamp = time();
    $url = $signature_url;
    $signature = sha1("jsapi_ticket={$ticket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}");

    exit(json_encode(array('status'=>'1','result'=> array('wxappid'=>SHARE_APPID,'wxtimestamp'=>$timestamp,'wxnonceStr'=>$nonceStr,'wxsignature'=>$signature,'ticket'=>$ticket,'url'=>$url))));
}

function is_weixin(){
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } else {
        return false;
    }
}


