<?php
namespace Admin\Controller\System;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Adm extends Controller
{
    public function index()
    {
        $this->is_login();

        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['adms'] = M::Admin('System\\Adm', 'findAdms', ['params'=>$param]);
        $count = M::Admin('System\\Adm', 'findAdmsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/System/Adm&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/System/Adm";

        $this->create_page();

        L::output(L::view('System\\AdmIndex', 'Admin', $this->data));
    }

    public function add_adm()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->create_page();

        L::output(L::view('System\\AdmAdd', 'Admin', $this->data));
    }

    public function do_add_adm()
    {
        if ($post = $this->validate_default()) {

            $admin_id = M::Admin('System\\Adm', 'addAdm', ['post'=>$post]);

            if ($admin_id > 0) {
                setcookie('success_info', '新增管理员成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增管理员成功'], JSON_UNESCAPED_UNICODE));
            } else {
                $errors ['other_error'] = '此账号已经被使用';

                exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));
            }
        }
        var_dump('aaa');
    }

    public function edit_adm()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['admin_id']);

        $admin_id = (int)$_GET['admin_id'];
        if (empty($admin_id))
            exit(header("location:{$this->data['entrance']}route=Admin/System/Adm{$this->data['url']}"));

        $adm_info = M::Admin('System\\Adm', 'findAdmByAdminId', ['admin_id'=>$admin_id]);
        if (empty($adm_info))
            exit(header("location:{$this->data['entrance']}route=Admin/System/Adm{$this->data['url']}"));

        if ($adm_info['group'] < $_SESSION['group'])
            exit(header("location:{$this->data['entrance']}route=Admin/System/Adm{$this->data['url']}"));

        $this->data['adm_info'] = $adm_info;

        $this->create_page();

        L::output(L::view('System\\AdmEdit', 'Admin', $this->data));
    }

    public function do_edit_adm()
    {
        if ($post = $this->validate_edit()) {
            M::Admin('System\\Adm', 'editAdm', ['post'=>$post]);

            setcookie('success_info', '编辑管理员成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'编辑管理员成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $admin_id = (int)$_POST['admin_id'];

        if (empty($admin_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少管理员标示'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('System\\Adm', 'removeAdm', ['admin_id'=>$admin_id]);

        if ($return) {
            setcookie('success_info', '删除管理员信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除管理员信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除管理员信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $admin_ids = $_POST['admin_ids'];

        if (count($admin_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少管理员标示'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('System\\Adm', 'removeAdms', ['admin_ids'=>$admin_ids]);

        if ($return) {
            setcookie('success_info', '删除多个管理员信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个管理员信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个管理员信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['group'])) {
                $errors ['group'] = '请选择权限组';
            }

            if (empty($post['username']) or mb_strlen($post['username']) < 6) {
                $errors ['username'] = '请填写账号并且不能少于6位';
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
            return false;
        }
    }

    public function validate_edit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            #如果是超级管理员,选择的权限组不能为空
            if ($_SESSION['group'] == 1 and empty($post['group'])) {
                $errors ['group'] = '请选择权限组';
            }

            if (!empty($post['password'])) {
                if (mb_strlen($post['real_password']) < 6) {
                    $errors ['password'] = '密码不能少于6位';
                }

                if (empty($post['c_password'])) {
                    $errors ['c_password'] = '确认密码不能为空';
                }

                if ($post['password'] != $post['c_password']) {
                    $errors ['c_password'] = '两次填写的密码不同';
                }
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}