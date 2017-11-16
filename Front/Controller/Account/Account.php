<?php
namespace Front\Controller\Account;

use Libs\Core\Controller AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;

class Account extends Controller
{
    public function login()
    {
        #$this->is_login();

        L::output(L::view('Account\\Login','Front',$this->data));
    }

    public function register()
    {
        L::output(L::view('Account\\Register', 'Front', $this->data));
    }

    public function do_login()
    {
        $username = htmlspecialchars($_GET['username']);
        $password = htmlspecialchars($_GET['password']);

        if (empty($username) or mb_strlen($username) < 4)
            exit(json_encode(['status'=>-1, 'result'=>'账号长度最少4位'], JSON_UNESCAPED_UNICODE));

        if (empty($password) or mb_strlen($password) < 10)
            exit(json_encode(['status'=>-1, 'result'=>'密码长度最少10位'], JSON_UNESCAPED_UNICODE));

        $result = M::Front('Account\\Account', 'login', ['username'=>$username, 'password'=>$password]);

        $_SESSION['uid'] = $result['id'];
        $_SESSION['uname'] = $result['username'];

        if (!empty($result)) {
            exit(json_encode(['status'=>1, 'result'=>'登陆成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'登陆失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_register()
    {
        $username = htmlspecialchars($_GET['username']);
        $password = htmlspecialchars($_GET['password']);
        $confirm_password = htmlspecialchars($_GET['confirm_password']);

        if (empty($username) or mb_strlen($username) < 4)
            exit(json_encode(['status'=>-1, 'result'=>'账号长度最少4位'], JSON_UNESCAPED_UNICODE));

        if (empty($password) or mb_strlen($password) < 10)
            exit(json_encode(['status'=>-1, 'result'=>'密码长度最少10位'], JSON_UNESCAPED_UNICODE));

        if (empty($confirm_password) or mb_strlen($confirm_password) < 10)
            exit(json_encode(['status'=>-1, 'result'=>'确认密码最少10位'], JSON_UNESCAPED_UNICODE));

        if ($password != $confirm_password)
            exit(json_encode(['status'=>-1, 'result'=>'两次密码输入不相同'], JSON_UNESCAPED_UNICODE));

        $result = M::Front('Account\\Account', 'register', ['username'=>$username, 'password'=>$password]);

        if ($result != -1) {
            exit(json_encode(['status'=>1, 'result'=>'注册成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'账号已经被使用'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_logout()
    {
        session_destroy();

        exit(json_encode(['status'=>1, 'result'=>'退出成功'], JSON_UNESCAPED_UNICODE));
    }
}