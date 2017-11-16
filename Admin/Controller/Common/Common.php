<?php
/**
 * 公共方法类
 */
namespace Admin\Controller\Common;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\ExtendsClass\ShareInterface AS Share;
use Libs\ExtendsClass\WxCommon AS Wx;
use Libs\ExtendsClass\Common AS ExtendsCommon;

class Common extends Controller
{
    /**
     * 分享接口
     */
    public function wx_share()
    {
        $signature_url = htmlspecialchars($_GET['signature_url']);

        $return = Share::create_signature($this->config['WX_CONF']['appid'],$this->config['WX_CONF']['appsecret'],$signature_url);

        exit($return);
    }

    /**
     * 微信 OAuth2认证第一步
     *
     * @param String $appid 微信公众号appid
     * @return Array $user 用户信息
     */
    public function wx_oauth2($appid = 'wx7ad3f7da78cbafc1')
    {
        #setcookie('openid','owCLhjnkyADvbMeGkwBQm-fnPcl0',time() + 3600 * 24 * 365);

        $openid = $_COOKIE['openid'] ?: '';

        if (empty($appid)) {
            trigger_error("Error: The Parameter 'appid' Is Empty",E_USER_WARNING);
            exit;
        }

        $user = '';
        if (!empty($openid)) {
            $user = M::Front('Common\\Common','get_user',array('openid'=>$openid));
        }

        if (empty($user)) {
            Wx::get_auto_code($appid, HTTP_SERVER.'route=Front/Common/Common/auth_callback');
        }

        var_dump($user);
        return $user;
    }

    /**
     * 微信 OAuth2认证 获取用户信息
     *
     * @param String $appid 微信公众号appid
     * @param String $appsecret 微信公众号appsecret
     *
     * @param string $appsecret
     */
    public function auth_callback($appid = 'wx7ad3f7da78cbafc1',$appsecret = '8e374858a924c808d370632d7e3294fd')
    {
        if (empty($appid) or empty($appsecret) or empty($_GET['code'])) {
            trigger_error("Error: The Parameter 'appid' Or 'appsecret' Or 'Code' Is Empty",E_USER_WARNING);
            exit;
        }

        $token_arr = wx::get_token($_GET['code'],$appid,$appsecret);

        $user = M::Front('Common\\Common','get_user',array('openid'=>$token_arr['openid']));

        if (empty($user)) {
            #非静默授权
            if ($token_arr['scope'] == 'snsapi_userinfo') {
                $user_info = Wx::get_wx_user_info($token_arr['access_token'],$token_arr['openid']);
                $wx_user_info = array(
                    'openid'=>$user_info['openid'],
                    'code_nickname'=>urlencode($user_info['nickname']),
                    'nickname'=>$user_info['nickname'],
                    'sex'=>$user_info['sex'],
                    'province'=>$user_info['province'],
                    'city'=>$user_info['city'],
                    'country'=>$user_info['country'],
                    'headimgurl'=>$user_info['headimgurl'],
                    'unionid'=>$user_info['unionid']
                );

                $bool = M::Front('Common\\Common','add_user',array('user_info'=>$wx_user_info));
            #静默授权
            } else {
                $wx_user_info = array(
                    'openid'=>$token_arr['openid']
                );

                $bool = M::Front('Common\\Common','add_user',array('user_info'=>$wx_user_info));
            }
        }

        if ($bool['last_id'] < 1) {
            trigger_error("Error:Oauth2 User add Failed",E_USER_WARNING);
            exit;
        }

        setcookie('openid',$wx_user_info['openid'],time() + 3600 * 24 * 365);

        exit(header('location:'.HTTP_SERVER.'route=Front/Common/Common/wx_oauth2'));
    }
}