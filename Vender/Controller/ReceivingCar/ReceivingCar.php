<?php
namespace Vender\Controller\ReceivingCar;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\BaiduOcr\OcrInit as Ocr;
use Libs\ExtendsClass\ImageResize as IR;

class ReceivingCar extends Controller
{
    public function index()
    {
        $this->create_page();

        L::output(L::view('ReceivingCar\\ReceivingCarIndex', 'Vender', $this->data));
    }

    public function register_success()
    {
        $this->create_page();

        L::output(L::view('ReceivingCar\\RegisterSuccess', 'Vender', $this->data));
    }

    public function do_add_receiving_car()
    {
        if ($post = $this->validate_step2()) {
            $reservation_id = M::Vender('ReceivingCar\\ReceivingCar', 'receiving_car', ['post'=>$post]);

            if ($reservation_id > 0) {
                exit(json_encode(['status'=>1, 'result'=>$reservation_id], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'接车失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * 百度Ocr图片识别
     */
    public function get_ocr()
    {
        #https://cloud.baidu.com/doc/OCR/OCR-PHP-SDK.html#.E8.A1.8C.E9.A9.B6.E8.AF.81.E8.AF.86.E5.88.AB 接口地址
        #http://blog.csdn.net/hu1991die/article/details/40585581
        #http://blog.csdn.net/hu1991die/article/details/41084153
        #https://yq.aliyun.com/articles/33569
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['base64_file']))
                exit(json_encode(['status'=>-1, 'result'=>'请上传图片'], JSON_UNESCAPED_UNICODE));

            $base64_file = explode(',', $_POST['base64_file']);
            if (empty($base64_file[1]))
                exit(json_encode(['status'=>-1, 'result'=>'请上传图片'], JSON_UNESCAPED_UNICODE));

            $ext_arr = explode(';', $base64_file[0]);
            $ext = explode('/', $ext_arr[0]);
            $ext = !empty($ext[1]) ? $ext[1] : 'jpeg';
            $file = base64_decode($base64_file[1]);

            $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

            $file_name = date('YmdHis', time()).$orderSn.mt_rand(100000, 999999).'.'.$ext;
            $file_path = ROOT_PATH.'Image'.DS.'upload'.DS.'vender'.DS.'receiving_car'.DS;

            file_put_contents($file_path.$file_name, $file);

//            $size = file_get_contents($file_path.$file_name);
//            print_r(strlen($size)/1024/1024);
//            exit;

            $scan = getimagesize($file_path.$file_name);
            $size = $scan[0] > $scan[1] ? $scan[0] : $scan[1];

            if ($size > 1024) {
                $n = 1;
                while ($n > 0.1) {
                    $n = $n - 0.1;

                    if (round($size * $n) < 1000) {
                        break;
                    }
                }

                $ir = new IR($file_path.$file_name);
                $ir->percent = $n;
                $ir->openImage();
                $ir->thumpImage();
                #$ir->showImage();
                $ir->saveImage($file_path.$file_name);
            }

            $ocr = new Ocr('10438599', 'cf4wHlLAC1V8OdkBpwiNYTbC', 'ZR5ImAmHf11bV7tGdz9aDKNvCAHoe3GA');
            $result = $ocr->analyze(file_get_contents($file_path.$file_name), 'vehicleLicense');

            $words_result = [];
            if (!empty($result['words_result'])) {
                foreach ($result['words_result'] as $key=>$value) {
                    if ($key == '品牌型号' and !empty($value['words']))
                        $words_result['brand_type'] = $value['words'];
                    if ($key == '发证日期' and !empty($value['words']))
                        $words_result['accepted_date'] = date('Y-m-d', strtotime($value['words']));
                    if ($key == '使用性质' and !empty($value['words']))
                        $words_result['use_type'] = $value['words'];
                    if ($key == '发动机号码' and !empty($value['words']))
                        $words_result['engine_number'] = $value['words'];
                    if ($key == '号牌号码' and !empty($value['words']))
                        $words_result['plate_number'] = $value['words'];
                    if ($key == '所有人' and !empty($value['words']))
                        $words_result['owner'] = $value['words'];
                    if ($key == '住址' and !empty($value['words']))
                        $words_result['address'] = $value['words'];
                    if ($key == '注册日期' and !empty($value['words']))
                        $words_result['registration_date'] = date('Y-m-d', strtotime($value['words']));
                    if ($key == '车辆识别代号' and !empty($value['words']))
                        $words_result['identification_number'] = $value['words'];
                    if ($key == '车辆类型' and !empty($value['words']))
                        $words_result['car_type'] = $value['words'];
                }
            }

            if (empty($words_result)) {
                exit(json_encode(['status'=>-1, 'result'=>'没有识别出数据，请手动填写或使用更清晰的图片，且大小不超过4MB'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>1, 'result'=>$words_result], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function validate_step2()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['tel'])) {
                $errors ['tel'] = '请填写手机号码';
            }

            if (empty($post['plate_number'])) {
                $errors ['plate_number'] = '请填写号牌号码';
            }

            if (empty($post['car_type'])) {
                $errors ['car_type'] = '请填写车辆类型';
            }

            if (empty($post['owner'])) {
                $errors ['owner'] = '请填写所有人';
            }

            if (empty($post['address'])) {
                $errors ['address'] = '请填写住址';
            }

            if (empty($post['use_type'])) {
                $errors ['use_type'] = '请填写使用性质';
            }

            if (empty($post['brand_type'])) {
                $errors ['brand_type'] = '请填写品牌型号';
            }

            if (empty($post['identification_number'])) {
                $errors ['identification_number'] = '请填写车辆识别代号';
            }

            if (empty($post['engine_number'])) {
                $errors ['engine_number'] = '请填写发动机号码';
            }

            if (!C::check_date_format($post['registration_date'])) {
                $errors ['registration_date'] = '请选择注册日期';
            }

            if (!C::check_date_format($post['accepted_date'])) {
                $errors ['accepted_date'] = '请选择受理日期';
            }

						/*
            if (empty($post['file_number'])) {
                $errors ['file_number'] = '请填写档案编号';
            }

            if (empty($post['people_number'])) {
                $errors ['people_number'] = '请填写核定人数';
            }

            if (empty($post['total_mass'])) {
                $errors ['total_mass'] = '请填写总质量';
            }

            if (empty($post['dimension'])) {
                $errors ['dimension'] = '请填写外观尺寸';
            }

            if (empty($post['description'])) {
                $errors ['description'] = '请填写备注';
            }
            */

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
        }
    }
}