<?php
/**
 * 微信分享功能生成签名类
 */
namespace Libs\ExtendsClass;

use Libs\ExtendsClass\WxCommon AS wx;
use Libs\Core\DbModel AS DB;

final class ShareInterface{
    /**
     * 微信签名生成类
     *
     * @param String $appid 微信appid
     * @param String $appsecret 微信appsecret
     * @param String $signature_url 要分享给别人的url
     *
     * @return Object json对象
     */
    public static function create_signature($appid = null,$appsecret = null,$signature_url = null)
    {
        $arr = self::get_token_and_ticket($appid,$appsecret);

        $return = wx::create_signature($appid,$arr['ticket'],$signature_url);

        return $return;
    }

    /**
     * 微信token和ticket的过期验证
     *
     * @param String $appid 微信appid,这里作为数据库的唯一标识，如果存在此appid的数据执行更新操作，否则插入操作
     * @param String $appsecret 微信appsecret
     *
     * @return String ticket
     */
    public static function get_token_and_ticket($appid = null,$appsecret = null)
    {
        if (empty($appid) or empty($appsecret)) return array('status'=>-1,'result'=>'Appid or Appsecret Is Empty');

        $time = time();

        $result = DB::get_one("SELECT token_value,ticket_value,update_time,expire FROM ".DB::$dp."token_and_ticket WHERE `appid`='{$appid}'");

        if ($result['update_time'] < $time - $result['expire']) {
            $token = wx::wx_get_token($appid,$appsecret);

            if (empty($token['access_token'])) return array('status'=>-1,'result'=>'AccessToken Is Empty');

            $ticket = wx::wx_get_ticket($token['access_token']);

            if (empty($ticket['ticket'])) return array('status'=>-1,'result'=>'Ticket Is Empty');

            if (empty($result)) {
                DB::insert("INSERT INTO ".DB::$dp."token_and_ticket SET `appid`='{$appid}',`appsecret`='{$appsecret}',`token_value`='{$token['access_token']}',`ticket_value`='{$ticket['ticket']}',`update_time`='{$time}'");
            }else{
                DB::update("UPDATE ".DB::$dp."token_and_ticket SET `token_value`='{$token['access_token']}',`ticket_value`='{$ticket['ticket']}',`update_time`='{$time}' WHERE `appid`='{$appid}'");
            }

            return array(
                'token' => $token['access_token'],
                'ticket' => $ticket['ticket'],
            );
        } else {
            return array(
                'token' => $result['token_value'],
                'ticket' => $result['ticket_value']
            );
        }
    }
}



