<?php
namespace Admin\Controller\Account;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;

class Account extends Controller
{
    public function login()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        L::output(L::view('Account\\Login', 'Admin' ,$this->data));
    }

    public function register()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();
        
        L::output(L::view('Account\\Register', 'Admin', $this->data));
    }

    public function do_login()
    {
        $username = htmlspecialchars($_GET['username']);
        $password = htmlspecialchars($_GET['password']);

        if (empty($username) or mb_strlen($username) < 6)
            exit(json_encode(['status'=>-1, 'result'=>'用户名长度最少6位'], JSON_UNESCAPED_UNICODE));

        if (empty($password))
            exit(json_encode(['status'=>-1, 'result'=>'请填写密码'], JSON_UNESCAPED_UNICODE));

        $result = M::Admin('Account\\Account', 'login', ['username'=>$username, 'password'=>$password]);

        if (!empty($result)) {
            $_SESSION['admin_id'] = $result['admin_id'];
            $_SESSION['admin_name'] = $result['username'];
            $_SESSION['group'] = $result['group'];

            exit(json_encode(['status'=>1, 'result'=>'登陆成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'账号或密码错误'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_register()
    {
        $username = htmlspecialchars($_GET['username']);
        $password = htmlspecialchars($_GET['password']);
        $confirm_password = htmlspecialchars($_GET['confirm_password']);

        if (empty($username) or mb_strlen($username) < 4)
            exit(json_encode(['status'=>-1, 'result'=>'账号长度最少4位'], JSON_UNESCAPED_UNICODE));

        if (empty($password) or mb_strlen($password) < 6)
            exit(json_encode(['status'=>-1, 'result'=>'密码长度最少10位'], JSON_UNESCAPED_UNICODE));

        if (empty($confirm_password) or mb_strlen($confirm_password) < 6)
            exit(json_encode(['status'=>-1, 'result'=>'确认密码最少10位'], JSON_UNESCAPED_UNICODE));

        if ($password != $confirm_password)
            exit(json_encode(['status'=>-1, 'result'=>'两次密码输入不相同'], JSON_UNESCAPED_UNICODE));

        $result = M::Admin('Account\\Account', 'register', ['username'=>$username, 'password'=>$password]);

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
        exit(header("location:{$this->data['entrance']}route=Admin/Account/Account/login"));
    }
}