<?php
/**
 * 模板加载类
 */
namespace Libs\Core;

final class Loader
{
    /**
     *  加载模板
     *
     *  @param String $path 根目录
     *  @param String $dir 文件夹名\\模板名称
     *  @param Array $data 参数
     *
     *  @return String $output 缓存页面
     */
    public static function view($dir = NULL, $path = NULL, $data = NULL)
    {
        if (empty($path)) $path = explode('/',$_GET['route']);
        if (!empty($dir)) $dir = explode('\\',$dir);

        $path = htmlspecialchars(ucfirst($path));

        $template = ROOT_PATH . $path . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . ucfirst($dir[0]) . DIRECTORY_SEPARATOR . ucfirst($dir[1]) . '.html';

        if (is_file($template)) {
            if (!empty($data)) extract($data);

            ob_start();

            require($template);

            $output = ob_get_contents();

            ob_end_clean();

            return $output;
        } else {
            trigger_error("Error: Template '{$template}' Not Found", E_USER_WARNING);
            return false;
        }
    }

    /**
     * 页面输出
     *
     * @param String $page 缓存页面
     *
     * @return Null
     */
    public static function output($page)
    {
        echo $page;
    }
}