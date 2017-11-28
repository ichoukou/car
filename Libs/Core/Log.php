<?php
/**
 * 日志类
 */
namespace Libs\Core;

class Log
{
    public static $conf = [];

    public static function wirte_db_log($info, $message)
    {
        #判断是否有log文件和写入权限
        if (is_file(self::$conf['error_db_log_path']) and is_writable(self::$conf['error_db_log_path'])) {
            $file = fopen(self::$conf['error_db_log_path'], "a+");

            $date     = "[".date('Y-m-d H:i:s', time())."]";
            $content  = "\r\n{$date} ERROR File:{$info['file']}";
            $content .= "\r\n{$date} ERROR Line:{$info['line']}";
            $content .= "\r\n{$date} ERROR Func:{$info['function']}";
            $content .= "\r\n{$date} ERROR Sql :{$info['args'][0]}";
            $content .= "\r\n{$date} ERROR Mess:{$message}";
            $content .= "\r\n";

            fwrite($file, $content);
            fclose($file);
        } else {
            exit(trigger_error("Error: File '" . self::$conf['error_db_log_path'] . "' does not exist or does not have permission to operate", E_USER_ERROR));
        }
    }

    public static function wirte_other_log($message)
    {
        #判断是否有log文件和写入权限
        if (is_file(self::$conf['error_other_log_path']) and is_writable(self::$conf['error_other_log_path'])) {
            $file = fopen(self::$conf['error_other_log_path'], "a+");

            $date     = "[".date('Y-m-d H:i:s', time())."]";
            $content  = "\r\n{$date} ERROR Mess:{$message}";
            $content .= "\r\n";

            fwrite($file, $content);
            fclose($file);
        } else {
            exit(trigger_error("Error: File '" . self::$conf['error_db_log_path'] . "' does not exist or does not have permission to operate", E_USER_ERROR));
        }
    }

    public static function wirte_other_info($message)
    {
        #判断是否有log文件和写入权限
        if (is_file(self::$conf['other_log_path']) and is_writable(self::$conf['other_log_path'])) {
            $file = fopen(self::$conf['other_log_path'], "a+");

            $date     = "[".date('Y-m-d H:i:s', time())."]";
            $content  = "\r\n{$date} ERROR Mess:{$message}";
            $content .= "\r\n";

            fwrite($file, $content);
            fclose($file);
        } else {
            exit(trigger_error("Error: File '" . self::$conf['error_db_log_path'] . "' does not exist or does not have permission to operate", E_USER_ERROR));
        }
    }
}