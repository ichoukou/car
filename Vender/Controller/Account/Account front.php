<?php
namespace Vender\Controller\Account;

use Libs\Core\ControllerVender AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Common as C;

class Account extends Controller
{
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

        $this->data['register_info'] = json_decode($_SESSION['register_info'], TURE);

        L::output(L::view('Account\\RegisterStep1', 'Vender', $this->data));
    }

    public function register_step2()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        $this->data['register_info'] = json_decode($_SESSION['register_info'], TURE);
        if (empty($this->data['register_info']))
            exit(header("location:{$this->data['entrance']}route=Vender/Account/Account/register"));

        L::output(L::view('Account\\RegisterStep2', 'Vender', $this->data));
    }

    public function get_code()
    {
        $_SESSION['register_code'] = 123;
    }

    public function do_add_register_step1()
    {
        if ($post = $this->validate_default()) {
            exit(json_encode(['status'=>1, 'result'=>'next'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_add_register_step2()
    {
        if ($post = $this->validate_edit()) {
            M::Vender('Company\\Company', 'editCompany', ['post'=>$post]);

            setcookie('success_info', '修改企业息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改企业息成功'], JSON_UNESCAPED_UNICODE));
        }
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

            $_SESSION['register_code'] = 123;
            if (empty($post['code']) or $post['code'] != $_SESSION['register_code']) {
                $errors ['code'] = '验证码错误';
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

            $_SESSION['register_info'] = json_encode($post, JSON_UNESCAPED_UNICODE);

            return $post;
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_edit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            $register_info = json_decode($_SESSION['register_info'], TURE);
            if (empty($register_info))
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

            if (empty($post['registration_date'])) {
                $errors ['registration_date'] = '请填写注册日期';
            }

            if (empty($post['accepted_date'])) {
                $errors ['accepted_date'] = '请填写受理日期';
            }

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

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_register()
    {
        $tel = htmlspecialchars($_GET['tel']);
        $password = htmlspecialchars($_GET['password']);
        $confirm_password = htmlspecialchars($_GET['confirm_password']);

        if (empty($tel) or !C::is_tel($tel))
            exit(json_encode(['status'=>-1, 'result'=>'请填写正确的11位手机号码'], JSON_UNESCAPED_UNICODE));

        if (empty($password) or mb_strlen($password) < 6)
            exit(json_encode(['status'=>-1, 'result'=>'密码长度最少10位'], JSON_UNESCAPED_UNICODE));

        if (empty($confirm_password) or mb_strlen($confirm_password) < 6)
            exit(json_encode(['status'=>-1, 'result'=>'确认密码最少10位'], JSON_UNESCAPED_UNICODE));

        if ($password != $confirm_password)
            exit(json_encode(['status'=>-1, 'result'=>'两次密码输入不相同'], JSON_UNESCAPED_UNICODE));

        $result = M::Vender('Account\\Account', 'register', ['tel'=>$tel, 'password'=>$password]);

        if ($result != -1) {
            exit(json_encode(['status'=>1, 'result'=>'注册成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'账号已经被使用'], JSON_UNESCAPED_UNICODE));
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