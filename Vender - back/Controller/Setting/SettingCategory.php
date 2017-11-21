<?php
namespace Admin\Controller\Setting;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class SettingCategory extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->data['module'] = $module = $_GET['module'];
        if (empty($this->data['setting_modules'][$module]))
            exit('配置栏目模块不存在');

        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['setting_categorys'] = M::Admin('Setting\\SettingCategory', 'findSettingCategorys', ['params'=>$param]);
        $count = M::Admin('Setting\\SettingCategory', 'findSettingCategoryCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/Setting/SettingCategory&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['module_name'] = $this->data['setting_modules'][$module];

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/Setting/SettingCategory&module={$module}";

        $this->create_page();

        L::output(L::view('Setting\\SettingCategoryIndex', 'Admin', $this->data));
    }

    public function add_setting_category()
    {
        $this->is_login();

        #不是超级管理员
        if ($this->data['session_info']['group'] != 1) {
            exit(header("location:{$this->data['entrance']}route=Admin/Home/Home"));
        }

        if (empty($this->data['setting_modules'][$_GET['module']]))
            exit('配置栏目模块不存在');

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['module'] = $_GET['module'];
        $this->data['module_name'] = $this->data['setting_modules'][$_GET['module']];

        $this->create_page();

        L::output(L::view('Setting\\SettingCategoryAdd', 'Admin', $this->data));
    }

    public function do_add_setting_category()
    {
        #不是超级管理员
        if ($this->data['session_info']['group'] != 1) {
            exit(json_encode(['status'=>-1, 'result'=>['other_error'=>'权限不够']], JSON_UNESCAPED_UNICODE));
        }

        if ($post = $this->validate_default()) {
            $setting_id = M::Admin('Setting\\SettingCategory', 'addSettingCategory', ['post'=>$post]);

            if ($setting_id > 0) {
                setcookie('success_info', "新增{$this->data['setting_modules'][$_POST['module']]}成功", time() + 60);

                exit(json_encode(['status'=>1, 'result'=>"新增配置信息成功"], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>"新增配置信息失败"], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_setting_category()
    {
        $this->is_login();

        #不是超级管理员
        if ($this->data['session_info']['group'] != 1) {
            exit(header("location:{$this->data['entrance']}route=Admin/Home/Home"));
        }

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['setting_category_id']);

        $setting_category_id = (int)$_GET['setting_category_id'];
        if (empty($setting_category_id))
            exit(header("location:{$this->data['entrance']}route=Admin/Setting/SettingCategory{$this->data['url']}"));

        $setting_category_info = M::Admin('Setting\\SettingCategory', 'findSettingCategoryByCategoryId', ['setting_category_id'=>$setting_category_id]);
        if (empty($setting_category_info))
            exit(header("location:{$this->data['entrance']}route=Admin/Setting/SettingCategory{$this->data['url']}"));

        $this->data['setting_category_info'] = $setting_category_info;

        $this->data['module_name'] = $this->data['setting_modules'][$setting_category_info['module']];

        $this->create_page();

        L::output(L::view('Setting\\SettingCategoryEdit', 'Admin', $this->data));
    }

    public function do_edit_setting_category()
    {
        #不是超级管理员
        if ($this->data['session_info']['group'] != 1) {
            exit(json_encode(['status'=>-1, 'result'=>['other_error'=>'权限不够']], JSON_UNESCAPED_UNICODE));
        }

        if ($post = $this->validate_edit()) {
            M::Admin('Setting\\SettingCategory', 'editSettingCategory', ['post'=>$post]);

            setcookie('success_info', "修改配置栏目信息成功", time() + 60);

            exit(json_encode(['status'=>1, 'result'=>"修改配置栏目信息成功"], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $setting_category_id = (int)$_POST['setting_category_id'];

        if (empty($setting_category_id))
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少配置栏目信息'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Setting\\SettingCategory', 'removeSettingCategory', ['setting_category_id'=>$setting_category_id]);

        if ($return) {
            setcookie('success_info', '删除配置栏目信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除配置栏目信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除配置栏目信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $setting_category_ids = $_POST['setting_category_ids'];

        if (count($setting_category_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少配置栏目信息'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Setting\\SettingCategory', 'removeSettingCategorys', ['setting_category_ids'=>$setting_category_ids]);

        if ($return) {
            setcookie('success_info', '删除多个配置栏目信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个配置栏目信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个配置栏目信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['module']) or empty($this->data['setting_modules'][$post['module']])) {
                $errors ['module'] = '缺少模块';
            }

            if (empty($post['key'])) {
                $errors ['key'] = '请填写Key';
            }

            if (empty($post['title'])) {
                $errors ['title'] = '请填写标题';
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

            if (empty($post['setting_category_id'])) {
                $errors ['setting_category_id'] = "缺少{$this->data['setting_modules'][$post['module']]}ID";
            }

            if (empty($post['module']) or empty($this->data['setting_modules'][$post['module']])) {
                $errors ['module'] = '缺少模块';
            }

            if (empty($post['key'])) {
                $errors ['key'] = '请填写Key';
            }

            if (empty($post['title'])) {
                $errors ['title'] = '请填写标题';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}