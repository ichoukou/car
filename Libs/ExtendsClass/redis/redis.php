<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/20
 * Time: 11:32
 */
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";

$redis = new Redis();
$redis->connect('127.0.0.1','6379');

$_GET['action']();

function t1(){
    global $redis;
    //选择或创建一个redis库 如果存在则选择 否则创建并使用
    $redis->select(1);
    //当sort与value同时重复或者value重复的时候 会覆盖之前的,key重复且value不重复的时候 添加一条新数
    $redis->zAdd('t',0,'aaa');
    $redis->zAdd('t',1,'bbb');
    $redis->zAdd('t',2,'ccc');
    $redis->zAdd('t',0,'aaa');

    $r = $redis->zRange('t',0,-1);//正序排序 array('val0', 'val1', 'val2')
//$redis->zDelete('t','ddd'); //根据key,val 删除
    $r = $redis->zRange('t',0,-1,true);//正序排序  array('val0' => sort0, 'val1' => sort1, 'val2' => sort2)
    dump($r);
    $r = $redis->zRevRange('t',0,-1);//倒序排序 array('val2', 'val1', 'val0')
    $r = $redis->zRevRange('t',0,-1,true);//倒序排序 array('val2' => sort2, 'val1' => sort1, 'val0' => sort0)
}

function t2(){
    global $redis;

    $redis->select(1);
    //flushdb 清空当前库
    //$redis->flushdb();
    $r = $redis->zRange('t',0,-1,true);//正序排序  array('val0' => sort0, 'val1' => sort1, 'val2' => sort2)
    dump($r);
}





