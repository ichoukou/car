<?php
namespace Front\Controller\Account;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\BaiduOcr\OcrInit as Ocr;
use Libs\ExtendsClass\ImageResize as IR;

class Account extends Controller
{
    public function index() {
        exit(header("location:{$this->data['entrance']}route=Front/Account/Account/login"));
    }

    public function login()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        L::output(L::view('Account\\Login', 'Front' ,$this->data));
    }

    public function do_login()
    {
        $tel = htmlspecialchars($_GET['tel']);
        $password = htmlspecialchars($_GET['password']);

        if (empty($tel) or !C::is_tel($tel))
            exit(json_encode(['status'=>-1, 'result'=>'请填写正确的11位手机号码'], JSON_UNESCAPED_UNICODE));

        if (empty($password))
            exit(json_encode(['status'=>-1, 'result'=>'请填写密码'], JSON_UNESCAPED_UNICODE));

        $result = M::Front('Account\\Account', 'login', ['tel'=>$tel, 'password'=>$password]);

        if (!empty($result)) {
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['tel'] = $result['tel'];

            exit(json_encode(['status'=>1, 'result'=>'登陆成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'账号或密码错误'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function register()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        $this->data['register_user_info'] = json_decode($_SESSION['register_user_info'], TRUE);

        L::output(L::view('Account\\RegisterStep1', 'Front', $this->data));
    }

    public function register_step2()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        $this->data['register_user_info'] = json_decode($_SESSION['register_user_info'], TRUE);
        if (empty($this->data['register_user_info']))
            exit(header("location:{$this->data['entrance']}route=Front/Account/Account/register"));

        L::output(L::view('Account\\RegisterStep2', 'Front', $this->data));
    }

    public function register_success()
    {
        $this->create_page();

        L::output(L::view('Account\\RegisterSuccess', 'Front', $this->data));
    }

//    public function do_add_register_step1()
//    {
//        if ($post = $this->validate_default()) {
//            $_SESSION['register_user_info'] = json_encode($post, JSON_UNESCAPED_UNICODE);
//            exit(json_encode(['status'=>1, 'result'=>'next'], JSON_UNESCAPED_UNICODE));
//        }
//    }

    public function do_add_register_step1()
    {
        if ($post = $this->validate_default()) {
            $user_id = M::Front('Account\\Account', 'register', ['post'=>$post]);

            if ($user_id > 0) {
                M::Front('Account\\Account', 'delSms', ['tel'=>$post['tel']]);

                exit(json_encode(['status'=>1, 'result'=>'注册成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'电话号码被使用'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function do_add_register_step2()
    {
        if ($post = $this->validate_step2()) {
            $user_id = M::Front('Account\\Account', 'register', ['post'=>$post]);

            if ($user_id > 0) {
                M::Front('Account\\Account', 'delSms', ['tel'=>$post['tel']]);
                $_SESSION['register_user_info'] = '';

                exit(json_encode(['status'=>1, 'result'=>'注册成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'电话号码被使用'], JSON_UNESCAPED_UNICODE));
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
            } elseif (M::Front('Account\\Account', 'findAccountByTel', ['tel'=>$post['tel']]) != '') {
                exit(json_encode(['status'=>-1, 'result'=>'此手机号码已经被使用'], JSON_UNESCAPED_UNICODE));
            }

            $sms_info = M::Front('Account\\Account', 'getSmsInfo', ['tel'=>$post['tel']]);
            if (!empty($sms_info) and $sms_info['send_time'] + 60 >= time()) {
                exit(json_encode(['status'=>-1, 'result'=>'1分钟内只能发送一次'], JSON_UNESCAPED_UNICODE));
            }

            $rand_number = mt_rand(100000, 999999);
            $content = "尊敬的用户，您的注册验证码是{$rand_number}，10分钟内有效。如非本人操作请忽略！【买着网】";

            $sms_id = M::Front('Account\\Account', 'addSms', ['tel'=>$post['tel'],'rand_number'=>$rand_number]);

            $return = C::sendSMS($post['tel'], $rand_number, $content);

            if ($return) {
                M::Front('Account\\Account', 'editSms', ['sms_id' => $sms_id, 'return_type' => 1]);
                exit(json_encode(['status'=>1, 'result'=>'短信发送成功请查收'], JSON_UNESCAPED_UNICODE));
            } else {
                M::Front('Account\\Account', 'editSms', ['sms_id' => $sms_id, 'return_type' => 2]);
                exit(json_encode(['status'=>-1, 'result'=>'短信发送失败'], JSON_UNESCAPED_UNICODE));
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
            $file_path = ROOT_PATH.'Image'.DS.'upload'.DS.'front'.DS.'register'.DS;

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

    public function getsize($size, $format = 'kb') {
        $p = 0;
        if ($format == 'kb') {
            $p = 1;
        } elseif ($format == 'mb') {
            $p = 2;
        } elseif ($format == 'gb') {
            $p = 3;
        }
        $size /= pow(1024, $p);
        return number_format($size, 3);
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
                $errors ['tel'] = '请填写正确的11位手机号码';
            } elseif (M::Front('Account\\Account', 'findAccountByTel', ['tel'=>$post['tel']]) != '') {
                $errors ['tel'] = '此手机号码已经被使用';
            }

            if (empty($post['code'])) {
                $errors ['code'] = '验证码错误';
            } else {
                $sms_info = M::Front('Account\\Account', 'validateSms', ['tel'=>$post['tel'], 'code'=>$post['code']]);
                if (empty($sms_info)) {
                    $errors ['tel'] = '验证码错误';
                } elseif ($sms_info['send_time'] + 600 < time()) {
                    $errors ['tel'] = '验证码已过期，请获取新的验证码';
                }
            }

            if (empty($post['password']) or mb_strlen($post['real_password']) < 6) {
                $errors ['password'] = '请填写密码并且不能少于6位';
            }

            if (empty($post['c_password'])) {
                $errors ['c_password'] = '确认密码不能为空';
            }

            if ($post['password'] != $post['c_password']) {
                $errors ['c_password'] = '两次填写的密码不同';
            }

//            if (empty($post['plate_number'])) {
//                $errors ['plate_number'] = '请填写号码号牌';
//            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_step2()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            $register_user_info = json_decode($_SESSION['register_user_info'], TRUE);
            if (empty($register_user_info))
                $errors ['other_error'] = '注册超时';

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

            $post['tel'] = $register_user_info['tel'];
            $post['password'] = $register_user_info['password'];

            return $post;
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_logout()
    {
        if (session_id()) {
            session_destroy();
        }

        #exit(json_encode(['status'=>1, 'result'=>'退出成功'], JSON_UNESCAPED_UNICODE));
        exit(header("location:{$this->data['entrance']}route=Front/Account/Account/login"));
    }
}