<?php
include_once "common/config.php";
include_once "db/conn.php";
include_once "startup.php";

if(isset($_GET['code'])){
    $token_arr = get_token($_GET['code'],APPID,APPSECRET);
}

if(empty($token_arr)){
    echo '授权失败1!';
    exit;
}

$now = time ();
setcookie ( 'openid', $token_arr ['openid'], $now + 3600 * 24 * 365 );
setcookie ( 'access_token', $token_arr ['access_token'], $now + 7200 - 200 );

$openid=isset($token_arr)?$token_arr['openid']:'';

if (empty ( $openid )) {
    $openid = isset ( $_COOKIE ['openid'] ) ? $_COOKIE ['openid'] : '';
}

if (empty ( $openid )) {
    return 'openid 为空。';
}

$arr = array('openid'=>$openid);

$user = query("SELECT * FROM ".DB_PREFIX."user WHERE `openid`='{$openid}'");
if(empty($user->rows)){
    $user_info = get_wxuserinfo($token_arr['access_token'],$openid);
    $now = time ();
    $wxuserinfo = array(
        #'openid'=>$openid,
        'openid'=>$user_info['openid'],
        'code_nickname'=>urlencode($user_info['nickname']),
        'nickname'=>$user_info['nickname'],
        'sex'=>$user_info['sex'],
        'province'=>$user_info['province'],
        'city'=>$user_info['city'],
        'country'=>$user_info['country'],
        'headimgurl'=>$user_info['headimgurl'],
        'create_time'=>date("Y-m-d H:i:s",$now),
    );

    $wxuserinfo = hsc($wxuserinfo);
    $wxuserinfodata = mres_filter_data($wxuserinfo);
    query("INSERT INTO ".DB_PREFIX."user SET ".$wxuserinfodata);
}

if (!empty($_GET['state'])) {
    exit(header('location:' . HTTP_URL . 'index.php?info_id=' . $_GET['state']));
} else {
    exit(header('location:' . HTTP_URL . 'index.php'));
}



