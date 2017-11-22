<?php
namespace Admin\Controller\Setting;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Setting extends Controller
{
    public function index()
    {
        $this->is_login();

        $validate_return = $this->validate_setting_category();
        if ($validate_return == -1) {
            setcookie('error_info', '缺少配置栏目信息标识', time() + 60);
            exit(header("location:{$this->data['entrance']}route=Admin/Setting/SettingCategory&module={$_GET['module']}"));
        } elseif($validate_return == -2) {
            setcookie('error_info', '没有找到配置栏目信息', time() + 60);
            exit(header("location:{$this->data['entrance']}route=Admin/Setting/SettingCategory&module={$_GET['module']}"));
        }

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'card_type']);

        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['settings_list'] = M::Admin('Setting\\Setting', 'findSettings', ['params'=>$param]);
        $count = M::Admin('Setting\\Setting', 'findSettingCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/Setting/Setting&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/Setting/Setting&setting_category_id={$_GET['setting_category_id']}";

        $this->create_page();

        L::output(L::view('Setting\\SettingIndex', 'Admin', $this->data));
    }

    public function add_setting()
    {
        $this->is_login();

        $validate_return = $this->validate_setting_category();

        if ($validate_return == -1) {
            exit('缺少配置栏目信息标识');
        } elseif($validate_return == -2) {
            exit('没有找到配置栏目信息');
        }

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'card_type']);

        $this->data['module_name'] = $this->data['setting_modules'][$this->data['setting_category_info']['module']];

        $this->create_page();

        L::output(L::view('Setting\\SettingAdd', 'Admin', $this->data));
    }

    public function do_add_setting()
    {
        if ($post = $this->validate_default()) {
            if ($this->data['setting_category_info']['key'] == 'setting_current_sensitive_period') {
                $post['times'] = (int)$post['times'] == 0 ? 1 : (int)$post['times'];
            }

            $setting_id = M::Admin('Setting\\Setting', 'addSetting', ['post'=>$post]);

            if ($setting_id > 0) {
                setcookie('success_info', "新增信息成功", time() + 60);

                exit(json_encode(['status'=>1, 'result'=>"新增信息成功"], JSON_UNESCAPED_UNICODE));
            } else {
                $errors ['other_error'] = 'Key被占用，不能重复';

                exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_setting()
    {
        $this->is_login();

        $this->validate_setting_category();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['setting_id']);

        $setting_id = (int)$_GET['setting_id'];
        if (empty($setting_id))
            exit(header("location:{$this->data['entrance']}route=Admin/Setting/Setting{$this->data['url']}"));

        $setting_info = M::Admin('Setting\\Setting', 'findSettingBySettingId', ['setting_id'=>$setting_id]);

        if (empty($setting_info))
            exit(header("location:{$this->data['entrance']}route=Admin/Setting/Setting{$this->data['url']}"));

        $this->data['setting_info'] = $setting_info;

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'card_type']);

        $this->data['module_name'] = $this->data['setting_modules'][$this->data['setting_category_info']['module']];

        $this->create_page();

        L::output(L::view('Setting\\SettingEdit', 'Admin', $this->data));
    }

    public function do_edit_setting()
    {
        if ($post = $this->validate_edit()) {
            if ($this->data['setting_category_info']['key'] == 'setting_current_sensitive_period') {
                $post['times'] = (int)$post['times'] == 0 ? 1 : (int)$post['times'];
            }

            $return = M::Admin('Setting\\Setting', 'editSetting', ['post'=>$post]);

            if ($return == -1) {
                $errors ['other_error'] = 'Key被占用，不能重复';
                exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));
            }

            setcookie('success_info', "修改配置选项信息成功", time() + 60);

            exit(json_encode(['status'=>1, 'result'=>"修改配置选项信息成功"], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $setting_id = (int)$_POST['setting_id'];

        if (empty($setting_id))
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少配置选项信息'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Setting\\Setting', 'removeSetting', ['setting_id'=>$setting_id]);

        if ($return) {
            setcookie('success_info', '删除配置选项信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除配置选项信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除配置选项信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $setting_ids = $_POST['setting_ids'];

        if (count($setting_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少配置选项信息'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Setting\\Setting', 'removeSettings', ['setting_ids'=>$setting_ids]);

        if ($return) {
            setcookie('success_info', '删除多个配置选项信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个配置选项信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个配置选项信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_setting_category($category_id = null)
    {
        $setting_category_id = !empty($category_id) ? (int)$category_id :(int)$_GET['setting_category_id'];
        if (empty($setting_category_id)) {
            return -1;
        } else {
            $this->data['setting_category_info'] = M::Admin('Setting\\SettingCategory', 'findSettingCategoryByCategoryId', ['setting_category_id'=>$setting_category_id]);

            if (empty($this->data['setting_category_info'])) {
               return -2;
            }
        }

        return 1;
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            $setting_category_id = (int)$post['setting_category_id'];
            $validate_return = $this->validate_setting_category($setting_category_id);

            if ($validate_return == -1) {
                $errors ['other_error'] = '缺少配置栏目信息标识';
            } elseif($validate_return == -2) {
                $errors ['other_error'] = '没有找到配置栏目信息';
            }

            if (empty($post['key'])) {
                $errors ['key'] = '请填写Key';
            }

            if (empty($post['value'])) {
                $errors ['value'] = '请填写标题';
            }

            if ($this->data['setting_category_info']['key'] == 'setting_user_card_type') {
                if (empty($post['parent_setting_id'])) {
                    $errors ['parent_setting_id'] = '请选择有效期';
                }

                $post['money'] = (float)$post['money'];
                if (!is_numeric($post['money']) and !is_float($post['money'])) {
                    $errors ['money'] = '办卡费用必须为纯数字或者浮点数';
                }
            }

            if ($this->data['setting_category_info']['key'] == 'setting_user_card_type_valid_period') {
                if (empty($post['months'])) {
                    $errors ['months'] = '请填写有效期月数';
                }
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

            $setting_id = (int)$post['setting_id'];
            $setting_category_id = (int)$post['setting_category_id'];

            $this->validate_setting_category($setting_category_id);

            if (empty($setting_id) or empty($setting_category_id)) {
                $errors ['other_error'] = '缺少唯一标识';
            }

            if (empty($post['key'])) {
                $errors ['key'] = '请填写Key';
            }

            if (empty($post['value'])) {
                $errors ['value'] = '请填写标题';
            }

            if ($this->data['setting_category_info']['key'] == 'setting_user_card_type') {
                if (empty($post['parent_setting_id'])) {
                    $errors ['parent_setting_id'] = '请选择有效期';
                }

                $post['money'] = (float)$post['money'];
                if (!is_numeric($post['money']) and !is_float($post['money'])) {
                    $errors ['money'] = '办卡费用必须为纯数字或者浮点数';
                }
            }

            if ($this->data['setting_category_info']['key'] == 'setting_user_card_type_valid_period') {
                if (empty($post['months'])) {
                    $errors ['months'] = '请填写有效期月数';
                }
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}