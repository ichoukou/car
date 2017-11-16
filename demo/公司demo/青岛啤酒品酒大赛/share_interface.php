<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/16
 * Time: 15:13
 */

include_once "common/config.php";
include_once "db/conn.php";
include_once "startup.php";

//include_once "common/wx_common.php";
//include_once "common/config.php";

if(!empty($_GET)){
    $_GET['action']();
}

function wx_get_share_param(){
    $get = $_GET;

    if(empty($get['signature_url'])) exit(json_encode(array('status'=>'-1','result'=>'signature_url is null。')));
    $time = time();

    $token = query("SELECT * FROM wx_token WHERE id=".DB_SHARE_TOKEN_ID);
    if($token->row['update_time'] < $time - 7000){
        $token_info = wx_get_token(SHARE_APPID,SHARE_APPSECRET);
        $data = mres_filter_data(array('value'=>$token_info['access_token'],'update_time'=>$time));
        $sql = "UPDATE wx_token SET ".$data." WHERE id=".DB_SHARE_TOKEN_ID;
        query($sql);
    }else{
        $token_info['access_token'] = $token->row['value'];
    }

    if(empty($token_info['access_token'])) exit(json_encode(array('status'=>'-1','result'=>'access_token is null。')));

    $ticket = query("SELECT * FROM wx_ticket WHERE id=".DB_SHARE_TICKET_ID);
    if($ticket->row['update_time'] < $time - 7000){
        $ticket_info = wx_get_jsapi_ticket($token_info['access_token']);
        $data1 = mres_filter_data(array('value'=>$ticket_info['ticket'],'update_time'=>$time));
        $sql1 = "UPDATE wx_ticket SET ".$data1." WHERE id=".DB_SHARE_TICKET_ID;
        query($sql1);
    }else{
        $ticket_info['ticket'] = $ticket->row['value'];
    }

    if(empty($ticket_info['ticket'])) exit(json_encode(array('status'=>'-1','result'=>'ticket is null。')));

    create_signature($ticket_info['ticket'],$get['signature_url']);
}
