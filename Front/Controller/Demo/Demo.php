<?php
/**
 * 部分php demo示例
 */
namespace Front\Controller\Demo;

use Libs\ExtendsClass\Email;

class Demo
{
    public function index()
    {
        echo 'this is demo';
        #发送邮件
        #$this->send_mail();
    }

    /**
     * 从pdf里面抓出首配图片，此扩展需要单独安装，
     * 教程1：http://www.jb51.net/article/50559.htm 编译安装
     * 教程2：http://blog.csdn.net/andy1219111/article/details/38335987 命令安装
     * 教程3：http://www.cnblogs.com/xiangxiaodong/archive/2013/12/23/3487008.html
     *
     * @param File $pdf 上传成功的pdf文件
     * @param String $path pdf的上传目录地址
     * @param String $name pdf上传成功后的名称
     * @param Int $page 读取第几页的图片
     *
     * @return String $return 保存成功后的图片地址
     */
    public function get_pdf_image($pdf,$path,$name,$page=0)
    {
        #检查扩展是否已经存在
        if (!extension_loaded('imagick')) {
            trigger_error("Error: The Extended 'Imagick' Does Not Exist",E_USER_WARNING);
            exit;
        }

        if (!file_exists($pdf)) {
            trigger_error("Error: The Pdf File '{$pdf}' Does Not Exist",E_USER_WARNING);
            exit;
        }

        if (empty($name)) {
            trigger_error("Error: The Pdf Name '{$name}' Is Empty",E_USER_WARNING);
            exit;
        }

        #实例化扩展
        $im = new Imagick();

        #设置图像分辨率
        $im->setResolution(500,500);

        #压缩比
        $im->setCompressionQuality(80);

        #设置读取pdf的第一页
        $im->readImage($pdf."[".$page."]");

        #改变图像的大小
        #$im->thumbnailImage(200, 100, true);

        #缩放大小图像
        $im->scaleImage(500,500,true);

        #创建和pdf文件名称对应的图片名称
        $filename = $name.'_cover_picture.png';

        #完整的pdf图片存在地址
        $filepath = $path.$filename;


        $return = '';
        if ($im->writeImage($filepath) == true)
        {
            $return  = $filename;
        }

        return $return;
    }

    /**
     * 发送邮件类
     *
     * @param String $to_email 接收方邮箱
     * @param String $to_name  邮件名称
     * @param String $subject  邮件主题
     * @param String $body     邮件内容
     *
     * @return Bool ;
     */
    public function send_mail($to_email = '403520515@qq.com',$to_name = '测试名称',$subject = '测试主题',$body = '测试内容')
    {
        Email::smtp_send_mail($to_email, $to_name,$subject, $body);
    }
}