<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/6/15
 * Time: 14:55
 */

/*
 * 检查文件路径是否存在 会逐个往上层去判断 一直检测出所有不存在的路径 并按照层级从小到大的顺序放在$dirs数组里 foreach 遍历 挨个创建目录文件夹 如果没有权限 返回false
 * @param $dir 文件绝对路径 调用方法之前 已经dirname过一次 所以这里的$dir是不带文件的路径
 * @param $dirs 按层级从小到大的顺序存放文件路径
 * return true or false
 */
function checkPathExists($dir = null){
    if(empty($dir)) return false;

    $dirs = array();
    while(!is_dir($dir)){
        array_unshift($dirs,$dir);

        $dir = dirname($dir);
    }

    if(!empty($dirs)){
        foreach($dirs as $path){
            $bool = @mkdir($path);

            if(!$bool){
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

function moveUploadedImage($aFiles, $fname, $new_path, $new_name, $maxsize=''){
    if(empty($aFiles) || empty($fname) || empty($new_path) || empty($new_name) || empty($maxsize)) return false;

    if ( $maxsize && ($aFiles[$fname]['size'] > $maxsize || $aFiles[$fname]['size'] == 0) ) {
        if (file_exists($aFiles[$fname]['tmp_name']) ) {
            unlink($aFiles[$fname]['tmp_name']);
        }
        return false;
    } else {
        $scan = getimagesize($aFiles[$fname]['tmp_name']);
        //获取后缀
        $mime=strtolower($scan['mime']);

        if ($mime == 'image/jpeg' || $mime == 'image/gif' || $mime == 'image/png' ){
            //判断图片后缀
            switch ($scan[2]) {

                case IMAGETYPE_GIF:$ext='.gif';break;

                case IMAGETYPE_JPEG:$ext='.jpg';break;

                case IMAGETYPE_PNG:$ext='.png';break;

                default: continue;
            }

            $path_and_name = $new_path . $new_name . $ext;

            if(!is_dir($new_path)){
                $bool = checkPathExists($new_path);

                if(!$bool) return false;
            }

            $status=move_uploaded_file( $aFiles[$fname]['tmp_name'], $path_and_name );

            if(!$status) return false;

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

function moveUploadedImages($aFiles, $fname, $new_path, $new_name, $maxsize=''){
    if(empty($aFiles) || empty($fname) || empty($new_path) || empty($new_name) || empty($maxsize)) return false;

    foreach($aFiles[$fname]['size'] as $size){
        if (($size > $maxsize ||$size == 0) ) return false;break;
    }

    if(!is_dir($new_path)){
        $bool = checkPathExists($new_path);

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

        $mime=strtolower($scan['mime']);

        $image_name = mt_rand(1,1000).$new_name.$ext;
        $filename=$new_path.$image_name;

        if ($mime == 'image/jpeg' || $mime == 'image/gif' || $mime == 'image/png' ){
            if ( move_uploaded_file($old, $filename ) ){
                $json[] = array(
                    'name'      =>              $image_name,
                    'status'        =>         1
                );
            }else{
                $json[] = array(
                    'status'        =>         -1,
                    'name'          => $this->request->files['image']['name'][$k]
                );
            }
        }else{
            $json[] = array(
                'status'        =>         -1,
                'name'          => $this->request->files['image']['name'][$k]
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
function hsc(&$data = null,$quote_style = ENT_QUOTES,$character_set = "UTF-8"){
    if(empty($data)) return false;

    if(is_array($data)){
        foreach($data as $key=>&$value){
            if(is_array($value)){
                hsc($value,$quote_style,$character_set);
            }else{
                $value = (isset($value) ? htmlspecialchars($value,$quote_style,$character_set) : '');
            }

//            if(!is_array($value)){
//                $data[$key] = (!empty($value) ? htmlspecialchars($value,$quote_style,$character_set) : '');
//            }else{
//                $data[$key] = $value;
//            }
        }
        return $data;
    }else{
        return htmlspecialchars($data,$quote_style,$character_set);
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
function hscd(&$data = null,$quote_style = ENT_QUOTES){
    if(empty($data)) return false;

    if(is_array($data)){
        foreach($data as $key=>&$value){
            if(is_array($value)){
                hscd($value,$quote_style);
            }else{
                $value = (isset($value) ? htmlspecialchars_decode($value,$quote_style) : '');
            }

//            if(!is_array($value)){
//                $data[$key] = (!empty($value) ? htmlspecialchars_decode($value,$quote_style) : '');
//            }else{
//                $data[$key] = $value;
//            }
        }
        return $data;
    }else{
        return htmlspecialchars_decode($data,$quote_style);
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
function mres_filter_data($data = null,$separator = ',',$like = array()){
    global $link;
    if(empty($data)) return '';
    if(!is_array($data)) return '';

    $sql = '';
    $num = 1;
    foreach($data as $key=>$val){
        if(isset($val)){
            $sql .= ($num != 1) ? " {$separator} " : ' ';

            //sql语句 in
            if(is_array($val)){
                $arr = implode(',',$val);
                foreach($arr as $k=>$v){
                    $arr[$k] = mysqli_real_escape_string($link,$v);
                }
                $sql .= "`{$key}` IN (".$arr.")";
                $num++;
                continue;
            }

            //sql语句 like和=
            if(!empty($like) && in_array($key,$like)){
                $sql .= "`{$key}` like '%".mysqli_real_escape_string($link,$val)."%'";
            }else{
                $sql .= "`{$key}`='".mysqli_real_escape_string($link,$val)."'";
            }

            $num++;
        }
    }

    return $sql;
}

function dump($data = null){
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}
function getIp(){
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $realip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $realip = $_SERVER['REMOTE_ADDR'];
    }

    return $realip;
}

function put_award_data($data) {
    //query("delete from ".DB_PREFIX."cfg where id >= 1");
    $data = serialize ( $data );
    $sql="update ".DB_PREFIX."cfg set value='{$data}'";
    query($sql);
}

function send_red_packet($openid = null,$money = 100){
    #https://pay.weixin.qq.com/wiki/doc/api/tools/cash_coupon.php?chapter=13_4&index=3 api地址
    $money = $money;//红包金额，单位分
    $mch_billno=MCHID.date('YmdHis').rand(1000, 9999);//订单号

    include_once(ROOT_PATH.DS.'pay'.DS.'WxHongBaoHelper.php');
    $commonUtil = new CommonUtil();
    $wxHongBaoHelper = new WxHongBaoHelper();

    $wxHongBaoHelper->setParameter("nonce_str", $commonUtil->create_noncestr());#随机字符串，丌长于 32 位
    $wxHongBaoHelper->setParameter("mch_billno", $mch_billno);#订单号
    $wxHongBaoHelper->setParameter("mch_id", MCHID);#商户号
    $wxHongBaoHelper->setParameter("wxappid", APPID);
    $wxHongBaoHelper->setParameter("nick_name", '海尔中央空调');#提供方名称
    $wxHongBaoHelper->setParameter("send_name", '海尔中央空调');#红包发送者名称(红包列表页面的红包名称)
    $wxHongBaoHelper->setParameter("re_openid", $openid);//相对于医脉互通的openid
    $wxHongBaoHelper->setParameter("total_amount", $money);//付款金额，单位分
    $wxHongBaoHelper->setParameter("min_value", $money);//最小红包金额，单位分
    $wxHongBaoHelper->setParameter("max_value", $money);//最大红包金额，单位分
    $wxHongBaoHelper->setParameter("total_num", 1);//红包収放总人数
    $wxHongBaoHelper->setParameter("wishing", '喜气多多');//红包祝福诧
    $wxHongBaoHelper->setParameter("client_ip", '112.126.68.182');//调用接口的机器 Ip 地址
    $wxHongBaoHelper->setParameter("act_name", '海尔中央空调');//活劢名称
    $wxHongBaoHelper->setParameter("remark", '海尔中央空调新春抢喜气活动');//备注信息
    $wxHongBaoHelper->setParameter("scene_id", 'PRODUCT_2');//发放红包使用场景 PRODUCT_2=抽奖

//		$wxHongBaoHelper->setParameter("logo_imgurl", RES_DOMAIN.'assets/images/getheadimg.jpg');//商户logo的url
//		$wxHongBaoHelper->setParameter("share_content", '一起来抢[腾讯]红包吧');//分享文案
//		$wxHongBaoHelper->setParameter("share_url", RES_DOMAIN);//分享链接
//		$wxHongBaoHelper->setParameter("share_imgurl", RES_DOMAIN.'assets/images/getheadimg.jpg');//分享的图片url

    $postXml = $wxHongBaoHelper->create_hongbao_xml();
    $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';

    $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);

    $responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);

    //$msg = $responseObj->return_code.','.$responseObj->return_msg;
    $responseArr = object_to_array($responseObj);

    #return array('code'=>$responseArr['return_code'],'msg'=>$responseArr['return_msg'],'err_code'=>$responseArr['err_code']);
    return $responseArr;
}

function object_to_array($obj){
    $_arr = is_object($obj)? get_object_vars($obj) :$obj;
    foreach ($_arr as $key => $val){
        $val=(is_array($val)) || is_object($val) ? object_to_array($val) :$val;
        $arr[$key] = $val;
    }
    return $arr;
}

function registered_token($length = 20)
{
    $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    $max = strlen($string) - 1;

    $token = '';

    for ($i = 0; $i < $length; $i++) {
        $token .= $string[mt_rand(0, $max)];
    }

    return $token;
}

function make_filter($params, $default_param = [], $filter_prefix = 'filter_')
{
    $result = [];

    if (!empty($params)) {
        $params = hsc($params);

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

function create_url($params, $ignore_param = [])
{
    if (empty($params))
        return '';

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


/**
 * desription 压缩图片
 * @param sting $imgsrc 图片路径
 * @param string $imgdst 压缩后保存路径
 */
function image_png_size_add($imgsrc, $imgdst, $dst_w, $dst_h){
    list($width,$height,$type)=getimagesize($imgsrc);
    $new_width = $dst_w;
    $new_height =$dst_h;
    switch($type){
        case 1:
            $giftype=check_gifcartoon($imgsrc);
            if($giftype){
                header('Content-Type:image/gif');
                $image_wp=imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefromgif($imgsrc);
                imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_wp, $imgdst,100);
                imagedestroy($image_wp);
            }

        case 2:
            header('Content-Type:image/jpeg');
            $image_wp=imagecreatetruecolor($new_width, $new_height);
            $image = imagecreatefromjpeg($imgsrc);
            imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($image_wp, $imgdst,100);
            imagedestroy($image_wp);
            break;
        case 3:
            header('Content-Type:image/png');
            $image_wp=imagecreatetruecolor($new_width, $new_height);
            $image = imagecreatefrompng($imgsrc);
            imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($image_wp, $imgdst,100);
            imagedestroy($image_wp);
            break;
    }
}

/**
 *
 * @param string $filename
 * @param string $width
 * @param string $height
 * @param string $quality
 * @param string $savepath
 * @return boolean
 */
function _make_thumb($filename='', $width=THUMB_WIDTH, $height=THUMB_HEIGHT, $savepath='./upload'){
    if(file_exists($filename)){
        //上传图片的尺寸
        $imagesize=getimagesize($filename);
        $imagewidth=$imagesize[0];
        $imageheight=$imagesize[1];
        $mime = $imagesize['mime'];
        //宽高比例
        $ratio = $imagewidth/$imageheight;

        //新建一个背景图片
        $bgimg = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($bgimg, 255, 255, 255);
        //填充背景色为白色
        imagefill($bgimg,0,0,$white);
        if($mime == 'image/gif'){
            $im = @imagecreatefromgif($filename); /* Attempt to open */
            $outfun = 'imagegif';
        }elseif($mime == 'image/png'){
            $im = @imagecreatefrompng($filename); /* Attempt to open */
            $outfun = 'imagepng';
        }else{
            $im = @imagecreatefromjpeg($filename); /* Attempt to open */
            $outfun = 'imagejpeg';
        }

        if($ratio > 1){
            //宽度较大
            if($imagewidth > $width){
                //缩放图片到背景图片上
                $new_width = $width;
                $new_height = ($width*$imageheight)/$imagewidth;
                $bg_y = ceil(abs(($height-$new_height)/2));
                imagecopyresampled($bgimg, $im, 0, $bg_y, 0, 0, $new_width, $new_height, $imagewidth, $imageheight);
            }else{
                //复制图片到背景图片上
                $copy = true;
            }
        }else{
            //高度较大
            if($imageheight > $height){
                //缩放图片
                $new_height = $height;
                $new_width = ($height*$imagewidth)/$imageheight;
                $bg_x = ceil(($width-$new_width)/2);
                imagecopyresampled($bgimg, $im, $bg_x, 0, 0, 0, $new_width, $new_height, $imagewidth, $imageheight);
            }else{
                //复制图片到背景图片上
                $copy = true;
            }
        }
        if($copy){
            //复制图片到背景图片上
            $bg_x = ceil(($width-$imagewidth)/2);
            $bg_y = ceil(($height-$imageheight)/2);
            imagecopy($bgimg, $im, $bg_x, $bg_y, 0, 0, $imagewidth, $imageheight);
        }
        $ext = _file_type($mime);
        $outfun($bgimg, $savepath.'/'.$ext);
        imagedestroy($bgimg);
        return $savepath.'/'.$ext;
    }else{
        return false;
    }
}

/**
 * 重新生成上传的文件名
 * @return string
 * @author zhao jinhan
 *
 */
function _file_type($filetype = null){
    switch($filetype)
    {
        case "image/jpeg":
            $fileextname  = "jpg";
            break;
        case "image/gif":
            $fileextname  = "gif";
            break;
        case "image/png":
            $fileextname  =  "png";
            break;
        default:
            $fileextname = false;
            break;
    }
    return $fileextname?date('YmdHis',time()).'.'.$fileextname:false;
}