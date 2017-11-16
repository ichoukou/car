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

$type = (int)$_GET['type'];
if ($type != 1 and $type != 2)
    $_GET['type'] = $type = 1;

$limit = 20;
$default_param = [
    'page'      => 1,
    'limit'     => $limit
];
$param = make_filter($_GET, $default_param);
$param['start'] = ($param['page'] - 1) * $limit;
$url = create_url($param);
$and = '';
$round_id = (int)$_GET['round_id'];
if (empty($round_id)) {
    exit(header('location:rounds.php?type=1'));
} else {
    $and .= " AND i.`round_id` = '{$round_id}' ";
}

if (!empty($param['filter_title']))
    $and .= " AND i.`title` LIKE '%{$param['filter_title']}%' ";

$list = query("SELECT i.*,r.title as round_title FROM ".DB_PREFIX."info AS i LEFT JOIN ".DB_PREFIX."rounds AS r ON i.round_id = r.round_id WHERE i.`deleted` = 1 AND r.deleted = 1 {$and} ORDER BY create_time LIMIT {$param['start']},{$param['limit']}");
$count = query("SELECT COUNT(*) total FROM ".DB_PREFIX."info AS i WHERE i.`deleted` = 1 {$and} ");

$pagination         = new Pagination();
$pagination->total  = $count->row['total'];
$pagination->page   = $param['page'];
$pagination->limit  = $param['limit'];
$pagination->url    = "index.php?page={page}{$url}";
$pagination         = $pagination->render();
$pagination_results = sprintf('显示 %d 到 %d / %d (总 %d 页)', ($count->row['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count->row['total'] - $param['limit'])) ? $count->row['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count->row['total'], ceil($count->row['total'] / $param['limit']));

$search_url = "index.php?round_id={$round_id}";

if ($type == 1) {
    $type_name = '初赛';
} else {
    $type_name = '决赛';
}

include_once "header.html";
include_once "left.html";
include_once "index.html";
include_once "footer.html";







