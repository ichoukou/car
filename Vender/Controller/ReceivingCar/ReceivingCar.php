<?php
namespace Vender\Controller\ReceivingCar;

use Libs\Core\ControllerVender AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\BaiduOcr\OcrInit as Ocr;
use Libs\ExtendsClass\ImageResize as IR;

class ReceivingCar extends Controller
{
    public function index()
    {
        if (empty($_GET['act']) or $_GET['act'] != 'back') {
            $_SESSION['receiving_car_info'] = '';
        }

        if (!empty($_SESSION['receiving_car_info'])) {
            $this->data['car_info'] = json_decode($_SESSION['receiving_car_info'], TRUE);
        }

        $this->create_page();

        L::output(L::view('ReceivingCar\\ReceivingCarIndex', 'Vender', $this->data));
    }

    public function view()
    {
        $car_info = json_decode($_SESSION['receiving_car_info'], TRUE);
        if (empty($car_info))
            exit(header("location:{$this->data['entrance']}route=Vender/ReceivingCar/ReceivingCar{$this->data['url']}"));

        $this->data['car_info'] = $car_info;

        $this->create_page();

        L::output(L::view('ReceivingCar\\ReceivingCarView', 'Vender', $this->data));
    }

    public function register_success()
    {
        $this->create_page();

        L::output(L::view('ReceivingCar\\RegisterSuccess', 'Vender', $this->data));
    }

    public function do_add_receiving_car_step1()
    {
        if ($post = $this->validate()) {
            $_SESSION['receiving_car_info'] = json_encode($post, JSON_UNESCAPED_UNICODE);

            exit(json_encode(['status'=>1, 'result'=>'成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_add_receiving_car_step2()
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
     * 手机验证码
     */
    public function get_code()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);

            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
                exit(json_encode(['status'=>-1, 'result'=>'请填写正确的11位手机号码'], JSON_UNESCAPED_UNICODE));
            } elseif (M::Vender('Company\\Company', 'findCompanyByTel', ['tel'=>$post['tel']]) != '') {
                exit(json_encode(['status'=>-1, 'result'=>'此手机号码已经被使用'], JSON_UNESCAPED_UNICODE));
            }

            $sms_info = M::Vender('Account\\Account', 'getSmsInfo', ['tel'=>$post['tel']]);
            if (!empty($sms_info) and $sms_info['send_time'] + 60 >= time()) {
                exit(json_encode(['status'=>-1, 'result'=>'1分钟内只能发送一次'], JSON_UNESCAPED_UNICODE));
            }

            $rand_number = mt_rand(100000, 999999);
            $content = "尊敬的企业用户，您的注册验证码是{$rand_number}，10分钟内有效。如非本人操作请忽略！【买着网】";

            $sms_id = M::Vender('ReceivingCar\\ReceivingCar', 'addSms', ['tel'=>$post['tel'],'rand_number'=>$rand_number]);

            $return = C::sendSMS($post['tel'], $rand_number, $content);

            if ($return) {
                M::Vender('Account\\Account', 'editSms', ['sms_id' => $sms_id, 'return_type' => 1]);
                exit(json_encode(['status'=>1, 'result'=>'短信发送成功请查收'], JSON_UNESCAPED_UNICODE));
            } else {
                M::Vender('Account\\Account', 'editSms', ['sms_id' => $sms_id, 'return_type' => 2]);
                exit(json_encode(['status'=>-1, 'result'=>'短信发送失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function validate()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
                $errors ['tel'] = '请填写正确的11位手机号码';
            }

            if (empty($post['code'])) {
                $errors ['code'] = '验证码错误';
            } else {
                $sms_info = M::Vender('ReceivingCar\\ReceivingCar', 'validateSms', ['tel'=>$post['tel'], 'code'=>$post['code']]);
                if (empty($sms_info)) {
                    $errors ['tel'] = '验证码错误';
                } elseif ($sms_info['send_time'] + 600 < time()) {
                    $errors ['tel'] = '验证码已过期，请获取新的验证码';
                }
            }

            if (empty($post['plate_number'])) {
                $errors ['plate_number'] = '请填写号牌号码';
            }

            if (empty($post['identification_number'])) {
                $errors ['identification_number'] = '请填写车辆识别代号';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_step2()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = json_decode($_SESSION['receiving_car_info'], TRUE);

            $errors = [];

            if (empty($post['plate_number'])) {
                $errors ['plate_number'] = '请填写号牌号码';
            }

            if (empty($post['identification_number'])) {
                $errors ['identification_number'] = '请填写车辆识别代号';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
        }
    }
}