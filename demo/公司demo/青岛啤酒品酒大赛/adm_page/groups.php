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

if (!empty($param['filter_title']))
    $and .= " AND `title` LIKE '%{$param['filter_title']}%' ";

$list = query("SELECT * FROM ".DB_PREFIX."groups WHERE `deleted` = 1 ORDER BY create_time DESC LIMIT {$param['start']},{$param['limit']}");
$count = query("SELECT COUNT(*) total FROM ".DB_PREFIX."groups WHERE `deleted` = 1 ");

$pagination         = new Pagination();
$pagination->total  = $count->row['total'];
$pagination->page   = $param['page'];
$pagination->limit  = $param['limit'];
$pagination->url    = "groups.php?page={page}{$url}";
$pagination         = $pagination->render();
$pagination_results = sprintf('显示 %d 到 %d / %d (总 %d 页)', ($count->row['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count->row['total'] - $param['limit'])) ? $count->row['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count->row['total'], ceil($count->row['total'] / $param['limit']));

$search_url = "groups.php?type={$type}";

include_once "header.html";
include_once "left.html";
include_once "groups.html";
include_once "footer.html";







