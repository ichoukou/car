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

$result = query("SELECT l.*,u.headimgurl,u.code_nickname FROM ".DB_PREFIX."log AS l LEFT JOIN  ".DB_PREFIX."user AS u ON l.openid=u.openid WHERE l.`deleted` = 1 AND l.`award_type` > 0 ");

$list = $result->rows;
$count = count($list);

#直接保存在当前文件相同目录下
#$objWriter->save("excel_text_".date('YmdHis',time()).$ext);

include_once "export_excel_options.php";

#设置第一个sheet下标
$objPHPExcel->setActiveSheetIndex(0);

#固定列数
$objPHPExcel->getActiveSheet()->setCellValue('A1',  '昵称');
$objPHPExcel->getActiveSheet()->setCellValue('B1',  '头像地址');
$objPHPExcel->getActiveSheet()->setCellValue('C1',  '姓名');
$objPHPExcel->getActiveSheet()->setCellValue('D1',  '电话');
$objPHPExcel->getActiveSheet()->setCellValue('E1',  '产品');
$objPHPExcel->getActiveSheet()->setCellValue('F1',  '日期');
$objPHPExcel->getActiveSheet()->setCellValue('G1',  '奖品名称');
$objPHPExcel->getActiveSheet()->setCellValue('H1',  '创建时间');

#第二行开始循环存储数据
for($i = 0;$i<$count;$i++){
    $j = $i+2;
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $j ,  urldecode($list[$i]['code_nickname']));
    $objPHPExcel->getActiveSheet()->setCellValue('B' . $j ,  $list[$i]['headimgurl']);
    $objPHPExcel->getActiveSheet()->setCellValue('C' . $j ,  $list[$i]['name']);
    $objPHPExcel->getActiveSheet()->setCellValue('D' . $j ,  $list[$i]['tel']);
    $objPHPExcel->getActiveSheet()->setCellValue('E' . $j ,  $products[$list[$i]['option_num']]);
    $objPHPExcel->getActiveSheet()->setCellValue('F' . $j ,  $list[$i]['date']);
    $objPHPExcel->getActiveSheet()->setCellValue('G' . $j ,  $list[$i]['description']);
    $objPHPExcel->getActiveSheet()->setCellValue('H' . $j ,  $list[$i]['create_time']);
}

#第一个sheet标题
$objPHPExcel->getActiveSheet()->setTitle('数据');

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