<?php
namespace Vender\Controller\Account;

use Libs\Core\ControllerVender AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\BaiduOcr\OcrInit as Ocr;

class Account extends Controller
{
    public function index() {

        exit(header("location:{$this->data['entrance']}route=Vender/Account/Account/login"));
    }

    public function login()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        L::output(L::view('Account\\Login', 'Vender' ,$this->data));
    }

    public function do_login()
    {
        $tel = htmlspecialchars($_GET['tel']);
        $password = htmlspecialchars($_GET['password']);

        if (empty($tel) or !C::is_tel($tel))
            exit(json_encode(['status'=>-1, 'result'=>'请填写正确的11位手机号码'], JSON_UNESCAPED_UNICODE));

        if (empty($password))
            exit(json_encode(['status'=>-1, 'result'=>'请填写密码'], JSON_UNESCAPED_UNICODE));

        $result = M::Vender('Account\\Account', 'login', ['tel'=>$tel, 'password'=>$password]);

        if (!empty($result)) {
            $_SESSION['company_id'] = $result['company_id'];
            $_SESSION['name'] = $result['name'];
            $_SESSION['tel'] = $result['tel'];
            $_SESSION['group'] = $result['group'];

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

        $this->data['register_vender_info'] = json_decode($_SESSION['register_vender_info'], TURE);

        L::output(L::view('Account\\RegisterStep1', 'Vender', $this->data));
    }

    public function register_step2()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        $this->data['register_vender_info'] = json_decode($_SESSION['register_vender_info'], TURE);
        if (empty($this->data['register_vender_info']))
            exit(header("location:{$this->data['entrance']}route=Vender/Account/Account/register"));

        L::output(L::view('Account\\RegisterStep2', 'Vender', $this->data));
    }

    public function do_add_register_step1()
    {
        if ($post = $this->validate_default()) {
            $_SESSION['register_vender_info'] = json_encode($post, JSON_UNESCAPED_UNICODE);
            exit(json_encode(['status'=>1, 'result'=>'next'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_add_register_step2()
    {
        if ($post = $this->validate_step2()) {
            $company_id = M::Vender('Account\\Account', 'register', ['post'=>$post]);

            if ($company_id > 0) {
                M::Vender('Account\\Account', 'delSms', ['tel'=>$post['tel']]);
                $_SESSION['register_vender_info'] = '';

                exit(json_encode(['status'=>1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
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
            } elseif (M::Vender('Company\\Company', 'findCompanyByTel', ['tel'=>$post['tel']]) != '') {
                exit(json_encode(['status'=>-1, 'result'=>'此手机号码已经被使用'], JSON_UNESCAPED_UNICODE));
            }

            $sms_info = M::Vender('Account\\Account', 'getSmsInfo', ['tel'=>$post['tel']]);
            if (!empty($sms_info) and $sms_info['send_time'] + 60 >= time()) {
                exit(json_encode(['status'=>-1, 'result'=>'1分钟内只能发送一次'], JSON_UNESCAPED_UNICODE));
            }

            $rand_number = mt_rand(100000, 999999);
            $content = "尊敬的企业用户，您的注册验证码是{$rand_number}，10分钟内有效。如非本人操作请忽略！【汽修网】";

            $sms_id = M::Vender('Account\\Account', 'addSms', ['tel'=>$post['tel'],'rand_number'=>$rand_number]);

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

    /**
     * 百度Ocr图片识别
     */
    public function get_ocr()
    {
        $ocr = new Ocr('10376062', 'aKPVvLlnx1uiPtGQ4oUd7RV3', 'jVscvETAswCo7KSoUiaiPHMS6Bz0PKFZ');
        $result = $ocr->analyze('http://hd.wechatdpr.com/jd/2017/1111/aaa.jpg');
        var_dump($result);
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
                $errors ['tel'] = '请填写正确的11位手机号码';
            } elseif (M::Vender('Company\\Company', 'findCompanyByTel', ['tel'=>$post['tel']]) != '') {
                $errors ['tel'] = '此手机号码已经被使用';
            }

            if (empty($post['code'])) {
                $errors ['code'] = '验证码错误';
            } else {
                $sms_info = M::Vender('Account\\Account', 'validateSms', ['tel'=>$post['tel'], 'code'=>$post['code']]);
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

            $register_vender_info = json_decode($_SESSION['register_vender_info'], TURE);
            if (empty($register_vender_info))
                $errors ['other_error'] = '注册超时';

            if (empty($post['name'])) {
                $errors ['name'] = '请填写名称';
            }

            if (empty($post['type'])) {
                $errors ['type'] = '请填写类型';
            }

            if (empty($post['address'])) {
                $errors ['address'] = '请填写住所';
            }

            if (empty($post['legal_person'])) {
                $errors ['legal_person'] = '请填写法定代表人';
            }

            if (empty($post['registered_capital'])) {
                $errors ['registered_capital'] = '请填写注册资本';
            }

            if (empty($post['date_time'])) {
                $errors ['date_time'] = '请填写成立日期';
            }

            if (empty($post['operating_period'])) {
                $errors ['operating_period'] = '请填写营业期限';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            $post['tel'] = $register_vender_info['tel'];
            $post['password'] = $register_vender_info['password'];

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
        exit(header("location:{$this->data['entrance']}route=Vender/Account/Account/login"));
    }
}