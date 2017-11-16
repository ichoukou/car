<?php
/**
 * 微信方法类
 */
namespace Libs\ExtendsClass;

final class WxCommon{
    /**
     *  微信auto2授权获得用户资料第一步
     *
     *  @param String $appid 认证过的微信公众号的appid
     *  @param String $redirect_uri 接收回调code的页面 这个页面要确保微信可以访问到 页面获得code值
     *  @param String $scope 授权方式 1.snsapi_base 静默授权 2.snsapi_userinfo 非静默授权
     *  @param String $state 任意输入一个值
     *
     *  @return Null
     */
    public static function get_auto_code($appid = null,$redirect_uri = null,$scope = "snsapi_userinfo",$state = "123")
    {
        if (empty($appid) or empty($redirect_uri)) {
            trigger_error("Error: 'The Parameter 'appid' Or 'redirect_uri' Is Empty'",E_USER_WARNING);
            exit;
        }

        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state={$state}#wechat_redirect";

        header('location:' . $url);
        exit;
    }

    /**
     *  微信auto2授权获得用户资料第二步
     *
     *  @param String $code 微信auto2授权第一步返回的code
     *  @param String $appid 认证过的微信公众号的appid
     *  @param String $appsecret 认证过的微信公众号的appsecret
     *
     *  @return Array array('access_token'=>?,'openid'=>?) / Bool false
     */
    public static function get_token($code = null,$appid = null,$appsecret = null)
    {
        if (empty($code) or empty($appid) or empty($appsecret)) {
            trigger_error("Error: 'The Parameter 'code' Or 'appid' Or 'appsecret' Is Empty'",E_USER_WARNING);
            exit;
        }

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
        $token_arr = self::http_request($url);

        if (isset($token_arr ['access_token'])) {
            return $token_arr;
        } else {
            trigger_error("Error: Return value 'oauth2_callback' Is Empty",E_USER_WARNING);
            exit;
        }
    }

    /**
     *  微信auto2授权获得用户资料第三步
     *
     *  @param String $access_token 微信auto2授权第二步返回的access_token
     *  @param String $openid 微信auto2授权第二步返回的openid
     *
     *  @return Array或Object 用户信息 / Bool false
     */
    public static function get_wx_user_info($access_token = null, $openid = null)
    {
        if (empty ( $access_token ) or empty ( $openid )) {
            trigger_error("Error: 'The Parameter 'access_token' Or 'openid' Is Empty'",E_USER_WARNING);
            exit;
        }

        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $info_arr = self::http_request($url);

        if (isset($info_arr ['openid'])) {
            return $info_arr;
        } else {
            trigger_error("Error: Return value 'user_info' Is Empty",E_USER_WARNING);
            exit;
        }
    }

    /**
     * Curl发送Http请求
     *
     * @param String $url   url地址
     * @param Array $data   数据参数
     * @param int $status   默认返回值是对象，如果此值为1，返回值时会转为数组
     * @param Array $header header头信息
     *
     * @return Array或Object 返回值 / Bool false
     */
    public static function http_request($url = null,$data = null,$status = 0,$header = null)
    {
        if (empty($url)) return false;

        $curl = curl_init ();
        curl_setopt ( $curl, CURLOPT_URL, $url );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if (!empty($header)) {
            //例子: $header = array("X-FDN-Auth:aaaaaaaaaaaaaaaaaa","content-type:application/json"))
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        $result_json = curl_exec ( $curl );
        curl_close ( $curl );

        if ($status == 0) $result_json = json_decode($result_json, TRUE);

        if (!empty($result_json))  return $result_json;

        return false;
    }

    /**
     *	分享接口第一步 获取token
     *
     *  @param String $appid 认证过的微信公众号的appid
     *  @param String $appsecret 认证过的微信公众号的appsecret
     *
     *	@return Array或Object 带有access_token的返回值 / Bool false
     */
    public static function wx_get_token($appid = null,$appsecret = null)
    {
        if(empty($appid) or empty($appsecret)){
            trigger_error("Error: 'The Parameter 'appid' Or 'appsecret' Is Empty'",E_USER_WARNING);
            exit;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";

        $token_info = self::http_request($url);

        if (!empty($token_info['access_token'])) {
            return $token_info;
        } else {
            trigger_error("Error: Return value 'access_token' Is Empty",E_USER_WARNING);
            exit;
        }
    }

    /**
     *	分享接口第二步 获取ticket
     *
     *	@param String $access_token 分享接口第一步的返回值
     *	@param String $type 类型
     *
     *  @return Array或Object 带有ticket的返回值
     */
    public static function wx_get_ticket($access_token = null,$type = 'jsapi')
    {
        if(empty($access_token)){
            trigger_error("Error: The Parameter 'access_token' Is Empty",E_USER_WARNING);
            exit;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$access_token}&type={$type}";
        $ticket_info = self::http_request($url);

        if (!empty($ticket_info['ticket'])) {
            return $ticket_info;
        } else {
            trigger_error("Error: Return value 'ticket' Is Empty",E_USER_WARNING);
            exit;
        }
    }

    /**
     *	分享接口第三步 生成签名
     *
     *	@param String $appid 公众号appid
     *	@param String $ticket 分享接口第二步的返回值
     *  @param String $nonceStr 是一个任意字符串
     *  @var   String $timestamp 是时间戳
     *  @param String $signature_url 要分享给别人的url
     *  @var   String $signature 生成的签名
     *
     *  @return Object 返回处理好的参数 在页面上的wx.config里使用
     */
    public static function create_signature($appid = null,$ticket = null,$signature_url = null,$nonceStr = "mcl")
    {
        if (empty($appid) or empty($ticket) or empty($signature_url)) {
            trigger_error("Error: The Parameter 'appid' Or 'ticket' Or 'signature_url' Is Empty",E_USER_WARNING);
            exit;
        }

        $ticket = $ticket;
        $nonceStr = $nonceStr;
        $timestamp = time();
        $url = $signature_url;
        $signature = sha1("jsapi_ticket={$ticket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}");

        #exit(json_encode(array('status'=>'1','result'=> array('wxappid'=>APPID,'wxtimestamp'=>$timestamp,'wxnonceStr'=>$nonceStr,'wxsignature'=>$signature,'ticket'=>$ticket,'url'=>$url))));
        return json_encode(array('status'=>'1','result'=> array('wxappid'=>$appid,'wxtimestamp'=>$timestamp,'wxnonceStr'=>$nonceStr,'wxsignature'=>$signature,'ticket'=>$ticket,'url'=>$url)));
    }
}
