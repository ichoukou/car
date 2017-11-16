<?php
/**
 * 使用phpmailer发邮件 实现确认php是否开启了socket(sockets)扩展
 * https://github.com/Synchro/PHPMailer 下载地址
 */
namespace Libs\ExtendsClass;

include_once "Phpmailer/PHPMailerAutoload.php";
include_once "Phpmailer/class.phpmailer.php";
include_once "Phpmailer/class.smtp.php";

class Email{
    public static function smtp_send_mail( $sendto_email,$sendto_name,$subject,$body)
    {
        if (empty($sendto_email) or empty($sendto_name) or empty($subject) or empty($body)) {
            trigger_error("Error: Please Fill In The Recipient, The Mail Name, The Message Subject, The Content",E_USER_WARNING);
            exit;
        }

        #SMTP 用户名注意：普通邮件认证不需要加 @域名
        $from_mail_smtp_name = '3266347095';
        #SMTP 用户密码
        $from_mail_smtp_password = 'mcl123';
        #发件人邮箱地址
        $from_mail = '3266347095@qq.com';
        #发件人名称
        $from_name = 'daylight';

        #$mail = new phpmailer();
        $mail = phpmail();
        #通过SMTP发送
        $mail->IsSMTP();
        #SMTP 服务器
        $mail->Host = "smtp.qq.com";
        #设置SMTP端口号,可能为 25、465、587
        #$mail->Port = 25;
        #打开SMTP验证
        $mail->SMTPAuth = true;
        $mail->Username = $from_mail_smtp_name;
        $mail->Password = $from_mail_smtp_password;
        $mail->From = $from_mail;
        $mail->FromName =  $from_name;
        #指定字符集 utf-8 or GB2312 or 其他
        $mail->CharSet = "utf-8";
        #加密方式
        $mail->Encoding = "base64";
        $mail->AddAddress($sendto_email,$sendto_name);
        $mail->AddReplyTo($from_mail,$from_name);
        #换行字数
        #$mail->WordWrap = 50;
        #附件 文件 or 压缩包 or 图片 or 其他
        #$mail->AddAttachment("/var/tmp/file.tar.gz");
        #发送邮件格式为html
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "
            <html><head>
            <meta http-equiv='Content-Language' content='zh-cn'>
            <meta http-equiv='Content-Type' content='text/html; charset=GB2312'>
            </head>
            <body>
                {$body}
            </body>
            </html>
            ";
        $mail->AltBody ="text/html";
        if (!$mail->Send()) {
            echo "邮件发送有误 <p>";
            echo "邮件错误信息: " . $mail->ErrorInfo;
            exit;
        } else {
            echo "$sendto_name 邮件发送成功!<br />";
        }
    }
}


