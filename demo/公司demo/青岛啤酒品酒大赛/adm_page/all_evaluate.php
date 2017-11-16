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

$filter_group = (int)$_GET['filter_group'];
$type = (int)$_GET['type'];
if ($type != 1 and $type != 2)
    $_GET['type'] = $type = 1;

if (!empty($filter_group)) {
    $and .= " AND g.`group_id` = '{$filter_group}' ";
}

$list = query("SELECT e.*,du.name,du.openid,g.title AS group_name,i.title,r.title AS round_title FROM ".DB_PREFIX."evaluate AS e " .
                  " LEFT JOIN ".DB_PREFIX."default_user AS du ON e.user_id=du.user_id " .
                  " LEFT JOIN ".DB_PREFIX."info AS i ON e.info_id=i.info_id " .
                  " LEFT JOIN ".DB_PREFIX."rounds AS r ON e.round_id=r.round_id " .
                  " LEFT JOIN ".DB_PREFIX."groups AS g ON du.group_id=g.group_id " .
                  " WHERE e.`type` = {$type} AND e.`deleted` = 1 {$and} ORDER BY e.create_time DESC LIMIT {$param['start']},{$param['limit']}");
$count = query("SELECT COUNT(*) total FROM ".DB_PREFIX."evaluate AS e " .
                   " LEFT JOIN ".DB_PREFIX."default_user AS du ON e.user_id=du.user_id " .
                   " LEFT JOIN ".DB_PREFIX."groups AS g ON du.group_id=g.group_id " .
                   " WHERE e.`type` = {$type} AND e.`deleted` = 1 {$and} ");

$pagination         = new Pagination();
$pagination->total  = $count->row['total'];
$pagination->page   = $param['page'];
$pagination->limit  = $param['limit'];
$pagination->url    = "all_evaluate.php?page={page}{$url}";
$pagination         = $pagination->render();
$pagination_results = sprintf('显示 %d 到 %d / %d (总 %d 页)', ($count->row['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count->row['total'] - $param['limit'])) ? $count->row['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count->row['total'], ceil($count->row['total'] / $param['limit']));

$search_url = "all_evaluate.php?type={$type}";

if (!empty($list->rows)) {
    $options = query("SELECT * FROM ".DB_PREFIX."options  WHERE `deleted` = 1 ");
    $options_arr = [];
    if (!empty($options->rows)) {
        foreach ($options->rows as $o) {
            $options_arr[$o['option_id']] = $o['title'];
        }
    }

    foreach ($list->rows as $k=>$i) {
        $list->rows[$k]['type_name'] = $group_types[$i['type']];

        if (!empty($i['likes'])) {
            $likes = explode(',', $i['likes']);
            foreach ($likes as $l) {
                if (!empty($list->rows[$k]['likes_name']))
                    $list->rows[$k]['likes_name'] .= ',';
                $list->rows[$k]['likes_name'] .= $options_arr[$l];
            }
        }

        if (!empty($i['hates'])) {
            $hates = explode(',', $i['hates']);
            foreach ($hates as $h) {
                if (!empty($list->rows[$k]['hates_name']))
                    $list->rows[$k]['hates_name'] .= ',';
                $list->rows[$k]['hates_name'] .= $options_arr[$h];
            }
        }
    }
}

$groups = query("SELECT * FROM ".DB_PREFIX."groups  WHERE `group_type` = '{$type}' ");

include_once "header.html";
include_once "left.html";
include_once "all_evaluate.html";
include_once "footer.html";







