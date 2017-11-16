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

$list = query("SELECT * FROM ".DB_PREFIX."cfg LIMIT {$param['start']},{$param['limit']}");
$count = query("SELECT COUNT(*) total FROM ".DB_PREFIX."cfg");

$pagination         = new Pagination();
$pagination->total  = $count->row['total'];
$pagination->page   = $param['page'];
$pagination->limit  = $param['limit'];
$pagination->url    = "cfg.php?page={page}{$url}";
$pagination         = $pagination->render();
$pagination_results = sprintf('显示 %d 到 %d / %d (总 %d 页)', ($count->row['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count->row['total'] - $param['limit'])) ? $count->row['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count->row['total'], ceil($count->row['total'] / $param['limit']));

$search_url = "cfg.php?";

$all_guess = query("SELECT * FROM ".DB_PREFIX."guess ");
$sum_guess = query("SELECT sum(probability) AS g FROM ".DB_PREFIX."cfg WHERE `deleted`=1");
$g = $sum_guess->row['g'] + $all_guess->row['num'];

include_once "header.html";
include_once "left.html";
include_once "cfg.html";
include_once "footer.html";







