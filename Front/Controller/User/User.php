<?php
namespace Front\Controller\User;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class User extends Controller
{
    public function index()
    {
        $this->is_login();

        $user_info = M::Front('User\\User', 'findUserBySession');

        if (empty($user_info)) {
            setcookie('success_info', '没有找到用户信息', time() + 60);
            exit(header("location:{$this->data['entrance']}route=Front/Account/Account/login"));
        }

        $this->data['settings'] = M::Front('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->data['user_info'] = $user_info;

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        L::output(L::view('User\\UserIndex', 'Front', $this->data));
    }

    public function do_edit_user()
    {
        if ($post = $this->validate_edit()) {
            M::Front('User\\User', 'editUser', ['post'=>$post]);

            setcookie('success_info', '修改会员息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改会员信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function edit_success()
    {
        $this->create_page();

        L::output(L::view('User\\UserEditSuccess', 'Front', $this->data));
    }

    public function validate_edit()
    {
        $this->is_login();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (!empty($post['password']) or !empty($post['c_password'])) {
                if (mb_strlen($post['real_password']) < 6) {
                    $errors ['password'] = '请填写密码并且不能少于6位';
                }

                if (empty($post['c_password'])) {
                    $errors ['c_password'] = '确认密码不能为空';
                }

                if ($post['password'] != $post['c_password']) {
                    $errors ['c_password'] = '两次填写的密码不同';
                }
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            $post['user_id'] = $_SESSION['user_id'];

            return $post;
        } else {
            return false;
        }
    }
}