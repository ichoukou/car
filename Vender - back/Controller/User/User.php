<?php
namespace Admin\Controller\User;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class User extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'user']);
        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['users'] = M::Admin('User\\User', 'findUsers', ['params'=>$param]);
        $count = M::Admin('User\\User', 'findUsersCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/User/User&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/User/User";

        $this->create_page();

        L::output(L::view('User\\UserIndex', 'Admin', $this->data));
    }

    public function add_user()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->create_page();

        L::output(L::view('User\\UserAdd', 'Admin', $this->data));
    }

    public function do_add_user()
    {
        if ($post = $this->validate_default()) {
            $user_id = M::Admin('User\\User', 'addUser', ['post'=>$post]);

            if ($user_id > 0) {
                setcookie('success_info', '新增会员信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增会员成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'新增会员失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_user()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['user_id']);

        $user_id = (int)$_GET['user_id'];
        if (empty($user_id))
            exit(header("location:{$this->data['entrance']}route=Admin/User/User{$this->data['url']}"));

        $user_info = M::Admin('User\\User', 'findUserByUserId', ['user_id'=>$user_id]);
        if (empty($user_info))
            exit(header("location:{$this->data['entrance']}route=Admin/User/User{$this->data['url']}"));

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->data['user_info'] = $user_info;

        $this->create_page();

        L::output(L::view('User\\UserEdit', 'Admin', $this->data));
    }

    public function do_edit_user()
    {
        if ($post = $this->validate_edit()) {
            M::Admin('User\\User', 'editUser', ['post'=>$post]);

            setcookie('success_info', '修改会员息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改会员信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $user_id = (int)$_POST['user_id'];

        if (empty($user_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少用户标识'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('User\\User', 'removeUser', ['user_id'=>$user_id]);

        if ($return) {
            setcookie('success_info', '删除会员信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除会员成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除会员失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $user_ids = $_POST['user_ids'];

        if (count($user_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少用户标识'], JSON_UNESCAPED_UNICODE));


        $return = M::Admin('User\\User', 'removeUsers', ['user_ids'=>$user_ids]);

        if ($return) {
            setcookie('success_info', '删除多个会员信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个会员成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个会员失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['user_group_id'])) {
                $errors ['user_group_id'] = '请选择会员等级';
            }

            if (empty($post['parent_name'])) {
                $errors ['parent_name'] = '请填写父、母姓名';
            }

//            if (empty($post['email']) or !C::is_email($post['email'])) {
//                $errors ['email'] = '请填写正确的邮箱';
//            } elseif(M::Admin('User\\User', 'findUserByEmail', ['email'=>$post['email']]) != '') {
//                $errors ['email'] = '此邮箱已被使用';
//            }

            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
                $errors ['tel'] = '请填写正确的11位手机号码';
            } elseif (M::Admin('User\\User', 'findUserByTel', ['tel'=>$post['tel']]) != '') {
                $errors ['tel'] = '此手机号码已经被使用';
            }

//            if (empty($post['newsletter'])) {
//                $errors ['newsletter'] = '请选择订阅邮件';
//            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }

    public function validate_edit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['user_id'])) {
                $errors ['other_error'] = '缺少会员标识';
            }

            if (empty($post['user_group_id'])) {
                $errors ['user_group_id'] = '请选择会员等级';
            }

            if (empty($post['parent_name'])) {
                $errors ['parent_name'] = '请填写父、母姓名';
            }

//            if (empty($post['email']) or !C::is_email($post['email'])) {
//                $errors ['email'] = '请填写正确的邮箱';
//            } else {
//                $user_info1 = M::Admin('User\\User', 'findUserByEmail', ['email'=>$post['email']]);
//                if (!empty($user_info1) and $user_info1['user_id'] != $post['user_id']) {
//                    $errors ['email'] = '此邮箱已被使用';
//                }
//            }

            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
                $errors ['tel'] = '请填写正确的11位手机号码';
            } else {
                $user_info2 = M::Admin('User\\User', 'findUserByTel', ['tel'=>$post['tel']]);
                if (!empty($user_info2) and $user_info2['user_id'] != $post['user_id']) {
                    $errors ['tel'] = '此手机号码已经被使用';
                }
            }

//            if (empty($post['newsletter'])) {
//                $errors ['newsletter'] = '请选择订阅邮件';
//            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}