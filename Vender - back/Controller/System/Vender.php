<?php
namespace Vender\Controller\System;

use Libs\Core\ControllerVender AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Vender extends Controller
{
    public function index()
    {
        $this->is_login();

        $page_config = M::Vender('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['venders'] = M::Vender('System\\Vender', 'findVenders', ['params'=>$param]);
        $count = M::Vender('System\\Vender', 'findVendersCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Vender/System/Vender&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Vender/System/Vender";

        $this->create_page();

        L::output(L::view('System\\VenderIndex', 'Vender', $this->data));
    }

    public function add_vender()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->create_page();

        L::output(L::view('System\\VenderAdd', 'Vender', $this->data));
    }

    public function do_add_vender()
    {
        if ($post = $this->validate_default()) {

            $vender_id = M::Vender('System\\Vender', 'addVender', ['post'=>$post]);

            if ($vender_id > 0) {
                setcookie('success_info', '新增管理员成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增管理员成功'], JSON_UNESCAPED_UNICODE));
            } else {
                $errors ['other_error'] = '此账号已经被使用';

                exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_vender()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['vender_id']);

        $vender_id = (int)$_GET['vender_id'];
        if (empty($vender_id))
            exit(header("location:{$this->data['entrance']}route=Vender/System/Vender{$this->data['url']}"));

        $vender_info = M::Vender('System\\Vender', 'findVenderByVenderId', ['vender_id'=>$vender_id]);
        if (empty($vender_info))
            exit(header("location:{$this->data['entrance']}route=Vender/System/Vender{$this->data['url']}"));

        if ($vender_info['group'] < $_SESSION['group'])
            exit(header("location:{$this->data['entrance']}route=Vender/System/Vender{$this->data['url']}"));

        $this->data['vender_info'] = $vender_info;

        $this->create_page();

        L::output(L::view('System\\VenderEdit', 'Vender', $this->data));
    }

    public function do_edit_vender()
    {
        if ($post = $this->validate_edit()) {
            M::Vender('System\\Vender', 'editVender', ['post'=>$post]);

            setcookie('success_info', '编辑管理员成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'编辑管理员成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $vender_id = (int)$_POST['vender_id'];

        if (empty($vender_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少管理员标示'], JSON_UNESCAPED_UNICODE));

        $return = M::Vender('System\\Vender', 'removeVender', ['vender_id'=>$vender_id]);

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

        $vender_ids = $_POST['vender_ids'];

        if (count($vender_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少管理员标示'], JSON_UNESCAPED_UNICODE));

        $return = M::Vender('System\\removeVender', 'removeVenders', ['vender_ids'=>$vender_ids]);

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

            if (empty($post['vender_id'])) {
                $errors ['other_error'] = '缺少管理员标识';
            }

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