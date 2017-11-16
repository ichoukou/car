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

$filter_name = $param['filter_name'];
$filter_award_status = $param['filter_award_status'];
$filter_create_time = $param['filter_create_time'];

if (!empty($filter_name)) {
    $and .= " AND l.`name` LIKE '%{$filter_name}%' ";
}

if (!empty($filter_award_status)) {
    if ($filter_award_status == 1) {
        $award_type = ' > 0';
    } else {
        $award_type = ' = 0';
    }
    $and .= " AND l.`award_type` {$award_type} ";
}

#$filter_create_time = empty($param['filter_create_time']) ? date('Y-m-d',time()) : $param['filter_create_time'];
if (!empty($filter_create_time)) {
    $and .= " AND l.`create_time` LIKE '%{$filter_create_time}%' ";
}

$list = query("SELECT l.*,u.headimgurl,u.code_nickname FROM ".DB_PREFIX."log AS l LEFT JOIN  ".DB_PREFIX."user AS u ON l.openid=u.openid WHERE l.`deleted` = 1 {$and} ORDER BY l.create_time DESC LIMIT {$param['start']},{$param['limit']}");
$count = query("SELECT COUNT(*) total FROM ".DB_PREFIX."log AS l WHERE l.`deleted` = 1 {$and} ");

$pagination         = new Pagination();
$pagination->total  = $count->row['total'];
$pagination->page   = $param['page'];
$pagination->limit  = $param['limit'];
$pagination->url    = "index.php?page={page}{$url}";
$pagination         = $pagination->render();
$pagination_results = sprintf('显示 %d 到 %d / %d (总 %d 页)', ($count->row['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count->row['total'] - $param['limit'])) ? $count->row['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count->row['total'], ceil($count->row['total'] / $param['limit']));

$search_url = "index.php?";

include_once "header.html";
include_once "left.html";
include_once "index.html";
include_once "footer.html";







