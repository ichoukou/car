<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

$action_return = $_COOKIE['action_return'];

if (!empty($action_return)) {
    $action_return = json_decode($_COOKIE['action_return'], TRUE);
    setcookie('action_return', '', -1);
}

include_once "../common/pagination.php";

$limit = 20;
$default_param = [
    'page'      => 1,
    'limit'     => $limit
];
$param = make_filter($_GET, $default_param);
$param['start'] = ($param['page'] - 1) * $limit;
$url = create_url($param);
$and = '';

$group_id = (int)$_GET['group_id'];
if (empty($group_id)) {
    exit(header('location:groups.php'));
} else {
    $and .= " AND u.`group_id` = '{$group_id}' ";
}

if (!empty($param['filter_name']))
    $and .= " AND u.`name` LIKE '%{$param['filter_name']}%' ";

$list = query("SELECT u.*,g.title,c.title as channel_title FROM ".DB_PREFIX."default_user AS u LEFT JOIN ".DB_PREFIX."groups AS g ON u.group_id=g.group_id LEFT JOIN ".DB_PREFIX."channels AS c ON c.channel_id=u.channel WHERE u.`deleted` = 1 {$and} LIMIT {$param['start']},{$param['limit']}");
$count = query("SELECT COUNT(*) total FROM ".DB_PREFIX."default_user AS u WHERE u.`deleted` = 1 {$and} ");

$pagination         = new Pagination();
$pagination->total  = $count->row['total'];
$pagination->page   = $param['page'];
$pagination->limit  = $param['limit'];
$pagination->url    = "user.php?page={page}{$url}";
$pagination         = $pagination->render();
$pagination_results = sprintf('显示 %d 到 %d / %d (总 %d 页)', ($count->row['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count->row['total'] - $param['limit'])) ? $count->row['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count->row['total'], ceil($count->row['total'] / $param['limit']));

$search_url = "user.php?group_id={$group_id}";

$channels = query("SELECT * FROM ".DB_PREFIX."channels WHERE `deleted` = 1");

include_once "header.html";
include_once "left.html";
include_once "user.html";
include_once "footer.html";







