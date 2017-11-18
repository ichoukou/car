<?php
/**
 * 公共方法类
 */
namespace Libs\ExtendsClass;

class Common
{
    /*
     * 检查文件路径是否存在 会逐个往上层去判断 一直检测出所有不存在的路径 并按照层级从小到大的顺序放在$dirs数组里 foreach 遍历 挨个创建目录文件夹 如果没有权限 返回false
     * @param $dir 文件绝对路径 调用方法之前 已经dirname过一次 所以这里的$dir是不带文件的路径
     * @param $dirs 按层级从小到大的顺序存放文件路径
     * return true or false
     */
    public static function check_path_exists($dir = null)
    {
        if (empty($dir)) return false;

        $dirs = array();
        while (!is_dir($dir)) {
            array_unshift($dirs,$dir);

            $dir = dirname($dir);
        }

        if (!empty($dirs)) {
            foreach ($dirs as $path) {
                $bool = @mkdir($path);

                if (!$bool) {
                    return false;
                    break;
                }
            }
        }

        return true;
    }

    /*
     * 上传单个图片方法
     * @param $aFiles 是 $_FILES
     * @param $fname 是 图片数组名称(对应着传图片的<input>标签的name)
     * @param $new_path 是 要移动到的位置
     * @param $new_name 是 要移动到的位置下的图片名称 不带后缀
     * @param $maxsize 是 图片大小
     * return 如果一切正确 返回图片名称
     */
    public static function move_uploaded_image($aFiles, $fname, $new_path, $new_name, $maxsize='')
    {
        if (empty($aFiles) or empty($fname) or empty($new_path) or empty($new_name) or empty($maxsize)) return false;

        if ($maxsize && ($aFiles[$fname]['size'] > $maxsize or $aFiles[$fname]['size'] == 0)) {
            if (file_exists($aFiles[$fname]['tmp_name'])) {
                unlink($aFiles[$fname]['tmp_name']);
            }
            return false;
        } else {
            $scan = getimagesize($aFiles[$fname]['tmp_name']);
            //获取后缀
            $mime = strtolower($scan['mime']);

            if ($mime == 'image/jpeg' or $mime == 'image/gif' or $mime == 'image/png' ){
                //判断图片后缀
                switch ($scan[2]) {

                    case IMAGETYPE_GIF:$ext='.gif';break;

                    case IMAGETYPE_JPEG:$ext='.jpg';break;

                    case IMAGETYPE_PNG:$ext='.png';break;

                    default: continue;
                }

                $path_and_name = $new_path . $new_name . $ext;

                if (!is_dir($new_path)) {
                    $bool = self::check_path_exists($new_path);

                    if(!$bool) return false;
                }

                $status=move_uploaded_file($aFiles[$fname]['tmp_name'], $path_and_name);

                if (!$status) return false;

            } else {
                return false;
            }
        }

        return $new_name . $ext;
    }

    /*
     * 上传多个图片方法
     * @param $aFiles 是 $_FILES
     * @param $fname 是 图片数组名称(对应着传图片的<input>标签的name)
     * @param $new_path 是 要移动到的位置
     * @param $new_name 是 要移动到的位置下的图片名称 不带后缀
     * @param $maxsize 是 图片大小
     * return 返回参数为图片名称和上传是否正确的数组
     */

    public static function move_uploaded_images($aFiles, $fname, $new_path, $new_name, $maxsize='')
    {
        if(empty($aFiles) or empty($fname) or empty($new_path) or empty($new_name) or empty($maxsize)) return false;

        foreach ($aFiles[$fname]['size'] as $size) {
            if (($size > $maxsize or $size == 0)) return false;break;
        }

        if (!is_dir($new_path)) {
            $bool = self::check_path_exists($new_path);

            if(!$bool) return false;
        }

        $json = array();
        foreach ($aFiles[$fname]['tmp_name'] as $k=>$old) {

            $scan = getimagesize($old);

            switch ($scan[2]) {

                case IMAGETYPE_GIF:$ext='.gif';break;

                case IMAGETYPE_JPEG:$ext='.jpg';break;

                case IMAGETYPE_PNG:$ext='.png';break;

                default: continue;
            }

            $mime = strtolower($scan['mime']);

            $image_name = mt_rand(1,1000).$new_name.$ext;
            $filename = $new_path.$image_name;

            if ($mime == 'image/jpeg' or $mime == 'image/gif' or $mime == 'image/png') {
                if (move_uploaded_file($old,$filename)) {
                    $json[] = array(
                        'name' => $image_name,
                        'status' => 1
                    );
                } else {
                    $json[] = array(
                        'status' => -1,
                        'name' => $_FILES['image']['name'][$k]
                    );
                }
            } else {
                $json[] = array(
                    'status' => -1,
                    'name' => $_FILES['image']['name'][$k]
                );
            }

            @unlink($old);
        }

        return $json;
    }

    /*
     *   字符转html实体
     *   @param $data 数据 可是一维数组 可是多维数组
     *   @param $quote_style 如何编码单引号和双引号
     *   ENT_COMPAT - 默认。仅编码双引号。
     *   ENT_QUOTES - 编码双引号和单引号。
     *   ENT_NOQUOTES - 不编码任何引号。
     *   @param $character_set 按照什么字符集来转义
     */
    public static function hsc(&$data, $quote_style = ENT_QUOTES, $character_set = "UTF-8")
    {
        if (empty($data)) return false;

        if (is_array($data)) {
            foreach ($data as $key=>&$value) {
                if (is_array($value)) {
                    self::hsc($value, $quote_style, $character_set);
                } else {
                    $value = (isset($value) ? htmlspecialchars($value, $quote_style, $character_set) : '');
                }
            }
            return $data;
        }else{
            return htmlspecialchars($data, $quote_style, $character_set);
        }
    }

    /*
     *   html实体转字符
     *   @param $data 数据 可是一维数组 可是多维数组
     *   @param $quote_style 如何编码单引号和双引号
     *   ENT_COMPAT - 默认。仅编码双引号。
     *   ENT_QUOTES - 编码双引号和单引号。
     *   ENT_NOQUOTES - 不编码任何引号。
     */
    public static function hscd(&$data, $quote_style = ENT_QUOTES)
    {
        if (empty($data)) return false;

        if (is_array($data)) {
            foreach ($data as $key=>&$value) {
                if (is_array($value)) {
                    self::hscd($value, $quote_style);
                } else {
                    $value = (isset($value) ? htmlspecialchars_decode($value, $quote_style) : '');
                }
            }
            return $data;
        }else{
            return htmlspecialchars_decode($data, $quote_style);
        }
    }

    /**
     * 验证邮箱格式
     */
    public static function is_email($email)
    {
        $mode = '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/';
        if (preg_match($mode, $email)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证手机格式
     */
    public static function is_tel($tel)
    {
        if (strlen($tel) != 11) return false;

        if (preg_match('/^0?(13|14|15|16|17|18)[0-9]{9}$/', $tel, $matches)) {
            return true;
        } else {
            return false;
        }
    }

///*
//     *  调用mysqli的real_escape_string()数据过滤非查询参数方法
//     *  @param $data 数组参数 必须是数组才有返回值 如果参数值是数组 默认是用逗号劈开 成字符串
//     *  return true or false
//     * */
//function filter_data($data = null){
//    if(empty($data)) return '';
//    if(!is_array($data)) return '';
//
//    $sql = '';
//    $num = 1;
//    foreach($data as $key=>$val){
//        if(!empty($val)){
//            $sql .= ($num != 1) ? "," : ' ';
//
//            $val = is_array($val) ? implode(',',$val) : $val;
//
//            $sql .= "`{$key}`='".mysql_real_escape_string($val)."'";
//
//            $num++;
//        }
//    }
//
//    return $sql;
//}

    /*
     *  调用mysqli的real_escape_string()数据过滤参数方法
     *  @param $data 数组参数 必须是数组才有返回值 如果数组里面嵌套二维数组 下面有判断自动处理成in()来过滤
     *  @param $separator 组成新语句的分隔符
     *  @like like语句查询判断条件 如果$key在这个数组里面 默认匹配成like查询 传参参数写法 -> array('like1','like2','like3',...)
     *  return 返回sql语句
     * */
    public static function mres_filter_data($data = null,$separator = ',',$like = array())
    {
        global $link;
        if (empty($data)) return '';
        if (!is_array($data)) return '';

        $sql = '';
        $num = 1;
        foreach ($data as $key=>$val) {
            if (isset($val)) {
                $sql .= ($num != 1) ? " {$separator} " : ' ';

                //sql语句 in
                if (is_array($val)) {
                    $arr = implode(',',$val);
                    foreach ($arr as $k=>$v) {
                        $arr[$k] = mysqli_real_escape_string($link,$v);
                    }
                    $sql .= "`{$key}` IN (".$arr.")";
                    $num++;
                    continue;
                }

                //sql语句 like和=
                if (!empty($like) and in_array($key,$like)) {
                    $sql .= "`{$key}` like '%" . mysqli_real_escape_string($link,$val) . "%'";
                } else {
                    $sql .= "`{$key}`='" . mysqli_real_escape_string($link,$val) . "'";
                }

                $num++;
            }
        }

        return $sql;
    }

    public static function get_ip()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }

        return $realip;
    }

    public static function object_to_array($obj)
    {
        $_arr = is_object($obj)? get_object_vars($obj) :$obj;
        foreach ($_arr as $key => $val) {
            $val = (is_array($val)) or is_object($val) ? self::object_to_array($val) : $val;
            $arr[$key] = $val;
        }

        return $arr;
    }

//    public static function make_filter($params, $default_param, $filter_prefix = 'filter_')
//    {
//        if (empty($params))
//            return [];
//
//        $params = self::hsc($params);
//
//        $result = $result['filter'] = [];
//        foreach($params as $filter_key=>$filter_value) {
//            if (stripos($filter_key, $filter_prefix) !== false) {
//                $result[$filter_key] = $result['filter'][$filter_key] = $filter_value;
//            } else {
//                if (!empty($default_param[$filter_key])) {
//                    $result[$filter_key] = $filter_value;
//                    unset($default_param[$filter_key]);
//                }
//            }
//        }
//
//        if (!empty($default_param))
//            $result = array_merge($result, $default_param);
//
//        return $result;
//    }

    public static function make_filter($params, $default_param = [], $filter_prefix = 'filter_')
    {
        if (!empty($params)) {
            $params = self::hsc($params);

            $result = [];
            foreach($params as $filter_key=>$filter_value) {
                if (!empty($default_param[$filter_key])) {
                    $result[$filter_key] = $filter_value;
                    unset($default_param[$filter_key]);
                } else {
                    $result[$filter_key] = $filter_value;
                }
            }
        }

        if (!empty($default_param))
            $result = array_merge($result, $default_param);

        return $result;
    }

    public static function create_url($params, $ignore_param = [])
    {
        if (empty($params))
            return '';

        if (isset($params['route']))
            unset($params['route']);

        if (isset($params['start']))
            unset($params['start']);

        if (isset($params['limit']))
            unset($params['limit']);

        if (isset($params['page']))
            unset($params['page']);

        $url = '';
        foreach($params as $url_key=>$url_value) {
            if (!in_array($url_key, $ignore_param))
                $url .= '&' . $url_key . '=' . urlencode(html_entity_decode($url_value, ENT_QUOTES, 'UTF-8'));
        }

        return $url;
    }

    public static function check_date_format($date)
    {
        #匹配日期格式
        if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {
            #检测是否为日期
            if (checkdate($parts[2],$parts[3],$parts[1])) {
                return true;
            } else {
                return false;
            }
        } else{
            return false;
        }
    }

    public static function check_time_format($time)
    {
        #匹配时间格式 HH:mm
        #if (preg_match('/^([0-2]{1})([0-9]{1}):([0-5]\d)$/', $time)) {
        if (preg_match('/^(0[0-9]{1}|1[0-9]{1}|2[0-3]{1}):([0-5]\d)$/', $time)) {
            return true;
        } else {
            return false;
        }
    }

    public static function get_salt($length = 20)
    {
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        $max = strlen($string) - 1;

        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $string[mt_rand(0, $max)];
        }

        return $token;
    }
}
