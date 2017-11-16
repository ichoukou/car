<?php
include_once "../common/config.php";
include_once "../db/conn.php";
include_once "../startup.php";
include_once "validate_login.php";

#第三方导入excel文件库
include_once "../PHP_Excel/PHPExcel_1.8.0_doc/Classes/PHPExcel.php";

#创建一个excel
$objPHPExcel = new PHPExcel();
$_GET['method'] = 'xls';
if($_GET['method'] == 'xlsx'){
    #导出.xlsx文件所需第三方库
    include_once "../PHP_Excel/PHPExcel_1.8.0_doc/Classes/PHPExcel/Writer/Excel2007.php";
    #保存excel—2007格式
    $ext = '.xlsx';
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
}else{
    #导出.xls文件所需第三方库
    include_once "../PHP_Excel/PHPExcel_1.8.0_doc/Classes/PHPExcel/Writer/Excel5.php";
    #非2007格式
    $ext = '.xls';
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
}

$info_id = (int)$_GET['info_id'];
$group = (int)$_GET['group'];
$and = '';
if(empty($info_id))
    exit;

if (!empty($group))
    $and = " AND g.`group_id` = '{$group}' ";

$result = query("SELECT e.*,du.name,du.openid,du.channel,g.title AS group_name,i.title,r.title AS round_title FROM ".DB_PREFIX."evaluate AS e " .
    " LEFT JOIN ".DB_PREFIX."default_user AS du ON e.user_id=du.user_id " .
    " LEFT JOIN ".DB_PREFIX."info AS i ON e.info_id=i.info_id " .
    " LEFT JOIN ".DB_PREFIX."rounds AS r ON e.round_id=r.round_id " .
    " LEFT JOIN ".DB_PREFIX."groups AS g ON du.group_id=g.group_id " .
    " WHERE e.`info_id` = {$info_id} AND e.`deleted` = 1 {$and} ORDER BY e.create_time DESC");

if (!empty($result->rows)) {
    $channels_list = query("SELECT * FROM ".DB_PREFIX."channels WHERE `deleted` = 1");
    $channels = [];
    if (!empty($channels_list->rows)) {
        foreach ($channels_list->rows as $c) {
            $channels[$c['channel_id']] = $c;
        }
    }

    $options = query("SELECT * FROM ".DB_PREFIX."options  WHERE `deleted` = 1  ORDER BY `option_type` ");
    $options_arr = [];
    if (!empty($options->rows)) {
        $all_likes = $all_hates = [];
        foreach ($options->rows as $o) {
            #$options_arr[$o['option_id']] = $o['title'];
            $options_arr[$o['option_id']]['title'] = $o['title'];
            $options_arr[$o['option_id']]['num'] = 0;
            if ($o['option_type'] == 1) {
                $all_likes[$o['title']] = 0;
            } else {
                $all_hates[$o['title']] = 0;
            }
        }
    }

    foreach ($result->rows as $k=>$i) {
        $result->rows[$k]['type_name'] = $group_types[$i['type']];

        if (!empty($i['likes'])) {
            $likes = explode(',', $i['likes']);
            foreach ($likes as $l) {
                if (!empty($result->rows[$k]['likes_name']))
                    $result->rows[$k]['likes_name'] .= ',';
                $result->rows[$k]['likes_name'] .= $options_arr[$l]['title'];
            }
        }

        if (!empty($i['hates'])) {
            $hates = explode(',', $i['hates']);
            foreach ($hates as $h) {
                if (!empty($result->rows[$k]['hates_name']))
                    $result->rows[$k]['hates_name'] .= ',';
                $result->rows[$k]['hates_name'] .= $options_arr[$h]['title'];
            }
        }
    }
}

$list = $result->rows;
$count = count($list);

#直接保存在当前文件相同目录下
#$objWriter->save("excel_text_".date('YmdHis',time()).$ext);

include_once "export_excel_options.php";

#设置第一个sheet下标
$objPHPExcel->setActiveSheetIndex(0);

#固定列数
$objPHPExcel->getActiveSheet()->setCellValue('A1',  '用户所在组');
$objPHPExcel->getActiveSheet()->setCellValue('B1',  '渠道');
$objPHPExcel->getActiveSheet()->setCellValue('C1',  '姓名');
$objPHPExcel->getActiveSheet()->setCellValue('D1',  '赛季');
$objPHPExcel->getActiveSheet()->setCellValue('E1',  '轮数');
$objPHPExcel->getActiveSheet()->setCellValue('F1',  '样品');
$objPHPExcel->getActiveSheet()->setCellValue('G1',  '评分');
$objPHPExcel->getActiveSheet()->setCellValue('H1',  '其他评价');
$objPHPExcel->getActiveSheet()->setCellValue('I1',  '创建时间');

#和上面的固定列数对应(列数从0开始计算)，用于计算特点和缺陷
$index = 8;
#保存每个option选项在excel里的位置
$option_index = [];
if (!empty($options->rows)) {
    foreach ($options->rows as $ok=>$ov) {
        $index++;
        $c= stringFromColumnIndex($index);
        $objPHPExcel->getActiveSheet()->setCellValue($c .'1', $ov['title']);
        $option_index[$ov['option_id']] = $c;
    }
}

#第二行开始循环存储数据
for($i = 0;$i<$count;$i++){
    $j = $i+2;
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $j ,  $list[$i]['group_name']);
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $j ,  $channels[$list[$i]['channel']]['title']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $j ,  $list[$i]['name']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $j ,  $list[$i]['type_name']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $j ,  $list[$i]['round_title']);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $j ,  $list[$i]['title']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $j ,  $list[$i]['score']);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $j ,  $list[$i]['content']);
    $objPHPExcel->getActiveSheet()->setCellValue('I' . $j ,  $list[$i]['create_time']);

    $o = '';
    if (!empty($list[$i]['likes']) and !empty($list[$i]['hates'])) {
        $o = $list[$i]['likes'] . ',' . $list[$i]['hates'];
    } elseif (!empty($list[$i]['likes'])) {
        $o = $list[$i]['likes'];
    } elseif (!empty($list[$i]['hates'])) {
        $o = $list[$i]['hates'];
    }

    if (!empty($o)) {
        $o_arr = explode(',', $o);
        foreach ($o_arr as $openid_id) {
            $objPHPExcel->getActiveSheet()->setCellValue($option_index[$openid_id] . $j ,  1);
        }
    }
}

#第一个sheet标题
$objPHPExcel->getActiveSheet()->setTitle('数据');

if (!empty($group)) {
    $clist = [];
    foreach ($list as $lk=>$lv) {
        #$clist[$lv['group_id'].'_'.$lv['round_id'].'_'.$lv['info_id']][] = $lv;
        $clist[$lv['channel']][] = $lv;
    }

    if (!empty($clist)) {
        #创建第二个sheet
        $objPHPExcel->createSheet();
        #设置第二个sheet下标
        $objPHPExcel->setActiveSheetIndex(1);

        #第二个sheet填充数据
        $objPHPExcel->getActiveSheet()->setCellValue('A1',  '赛季：');
        $objPHPExcel->getActiveSheet()->setCellValue('B1',  '用户所在组：');
        $objPHPExcel->getActiveSheet()->setCellValue('C1',  '渠道：');
        $objPHPExcel->getActiveSheet()->setCellValue('D1',  '轮数：');
        $objPHPExcel->getActiveSheet()->setCellValue('E1',  '样品：');
        $objPHPExcel->getActiveSheet()->setCellValue('F1',  '平均分数：');
        $objPHPExcel->getActiveSheet()->setCellValue('G1',  '换算分数：');

        #第二个sheet的特点和缺陷标题
        $option_index = 7;
        if (!empty($options_arr)) {
            foreach ($options_arr as $ok=>$ov) {
                $c= stringFromColumnIndex($option_index);
                $objPHPExcel->getActiveSheet()->setCellValue($c . 1 ,  $ov['title']);
                $option_index++;
            }
        }

        #其他评价标题
        $c= stringFromColumnIndex($option_index);
        $objPHPExcel->getActiveSheet()->setCellValue($c . 1 ,  '其他评价');

        #zindex是横排索引，tindex竖排索引
        $zindex = 5;
        $tindex = 2;
        $channel_id = 0;
        foreach ($clist as $ck=>$cv) {
            $score_title = stringFromColumnIndex($zindex);
            $zindex++;
            $channel_score_title = stringFromColumnIndex($zindex);
            foreach ($cv as $cvk=>$cvv) {
                $channel_id = $cvv['channel'];
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $tindex,  $cvv['type_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $tindex,  $cvv['group_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $tindex,  $channels[$cvv['channel']]['title']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $tindex,  $cvv['round_title']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $tindex,  $cvv['title']);

                $score = 0;
                $this_option = $options_arr;
                $contents = [];

                foreach ($cv as $ck1=>$cv1) {
                    if (!empty($cv1['likes'])) {
                        $likes_arr = explode(',', $cv1['likes']);
                        foreach ($likes_arr as $like_k=>$like_v) {
                            if (!empty($this_option[$like_v])) {
                                $this_option[$like_v]['num']++;
                            }
                        }
                    }

                    if (!empty($cv1['hates'])) {
                        $hates_arr = explode(',', $cv1['hates']);
                        foreach ($hates_arr as $hate_k=>$hate_v) {
                            if (!empty($this_option[$hate_v])) {
                                $this_option[$hate_v]['num']++;
                            }
                        }
                    }

                    $score += $cv1['score'];

                    if (!empty($cv1['content']))
                        array_push($contents, $cv1['content']);
                }
            }

            #特点和缺陷添加
            foreach ($this_option as $this_k=>$this_v) {
                $zindex++;
                $c = stringFromColumnIndex($zindex);

                $objPHPExcel->getActiveSheet()->setCellValue($c . $tindex,  $this_v['num']);
            }

            #评论添加
            if (!empty($contents)) {
                foreach ($contents as $c_value) {
                    $zindex++;
                    $c = stringFromColumnIndex($zindex);

                    $objPHPExcel->getActiveSheet()->setCellValue($c . $tindex,  $c_value);
                }
            }

            #求平均分
            $average_score = round($score / count($cv), 2);
            $objPHPExcel->getActiveSheet()->setCellValue($score_title . $tindex,  $average_score);
            #求换算分
            $channel_score = round($score / count($cv), 2) * $channels[$channel_id]['num'];
            $objPHPExcel->getActiveSheet()->setCellValue($channel_score_title . $tindex,  $channel_score);

            #横排索引重置，竖排索引增加
            $zindex = 5;
            $tindex++;
        }

        #第二个sheet标题
        $objPHPExcel->getActiveSheet()->setTitle('统计');
    }
}

#设置协议
header("Pragma: public");
header("Expires: 0");
header("Cache-Control:must-revalidate, post-check=0,pre-check=0");
header("Content-Type:application/force-download");
header("Content-Type:application/vnd.ms-execl");
header("Content-Type:application/octet-stream");
header("Content-Type:application/download");
header("Content-Disposition:attachment;filename=数据_".date('YmdHis',time()).$ext);
header("Content-Transfer-Encoding:binary");

//在默认sheet后，创建一个worksheet
//$objPHPExcel->createSheet();
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;