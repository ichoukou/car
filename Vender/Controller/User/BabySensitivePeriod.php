<?php
namespace Admin\Controller\User;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class BabySensitivePeriod extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'baby_sensitive_period']);
        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['sensitive_periods'] = M::Admin('User\\BabySensitivePeriod', 'findSensitivePeriods', ['params'=>$param]);
        $count = M::Admin('User\\BabySensitivePeriod', 'findSensitivePeriodsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/User/BabySensitivePeriod&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/User/BabySensitivePeriod";

        $this->create_page();

        L::output(L::view('User\\SensitivePeriodIndex', 'Admin', $this->data));
    }

    public function add_sensitive_period()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'baby_sensitive_period']);

        $this->create_page();

        L::output(L::view('User\\SensitivePeriodAdd', 'Admin', $this->data));
    }

    public function do_add_sensitive_period()
    {
        if ($post = $this->validate_default()) {
            $sensitive_period_id = M::Admin('User\\BabySensitivePeriod', 'addBabySensitivePeriod', ['post'=>$post]);

            if ($sensitive_period_id > 0) {
                setcookie('success_info', '新增宝宝敏感期信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增宝宝敏感期信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'新增宝宝敏感期信息失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_sensitive_period()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['sensitive_period_id']);

        $sensitive_period_id = (int)$_GET['sensitive_period_id'];
        if (empty($sensitive_period_id))
            exit(header("location:{$this->data['entrance']}route=Admin/User/BabySensitivePeriod{$this->data['url']}"));

        $sensitive_period_info = M::Admin('User\\BabySensitivePeriod', 'findSensitivePeriodBySensitivePeriodId', ['sensitive_period_id'=>$sensitive_period_id]);
        if (empty($sensitive_period_info))
            exit(header("location:{$this->data['entrance']}route=Admin/User/BabySensitivePeriod{$this->data['url']}"));

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'baby_sensitive_period']);

        $this->data['sensitive_period_info'] = $sensitive_period_info;

        $this->create_page();

        L::output(L::view('User\\SensitivePeriodEdit', 'Admin', $this->data));
    }

    public function do_edit_sensitive_period()
    {
        if ($post = $this->validate_edit()) {
            M::Admin('User\\BabySensitivePeriod', 'editSensitivePeriod', ['post'=>$post]);

            setcookie('success_info', '修改宝宝敏感期信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改宝宝敏感期信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $sensitive_period_id = (int)$_POST['sensitive_period_id'];

        if (empty($sensitive_period_id))
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少用户标示'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('User\\BabySensitivePeriod', 'removeSensitivePeriod', ['sensitive_period_id'=>$sensitive_period_id]);

        if ($return) {
            setcookie('success_info', '删除宝宝敏感期信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除宝宝敏感期信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除宝宝敏感期信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $sensitive_period_ids = $_POST['sensitive_period_ids'];

        if (count($sensitive_period_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少用户标示'], JSON_UNESCAPED_UNICODE));


        $return = M::Admin('User\\BabySensitivePeriod', 'removeSensitivePeriods', ['sensitive_period_ids'=>$sensitive_period_ids]);

        if ($return) {
            setcookie('success_info', '删除多个会员信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个会员成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个会员失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function autocomplete_baby()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = [];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $result = M::Admin('User\\BabySensitivePeriod', 'autocompleteFindBabys', ['post'=>$param]);

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function autocomplete_sensitive_period()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = ['module'=>'baby_sensitive_period'];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $result = M::Admin('User\\BabySensitivePeriod', 'autocompleteFindSensitivePeriodSettings', ['post'=>$param]);

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            $baby_id = (int)$post['baby_id'];

            if (empty($baby_id)) {
                $errors ['baby_id'] = '请选择宝宝';
            }

            if (count($post['setting_id']) < 1) {
                $errors ['setting_id'] = '请选择敏感期';
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

            $sensitive_period_id = (int)$post['sensitive_period_id'];

            if (empty($sensitive_period_id)) {
                $errors ['other_error'] = '缺少敏感期标识';
            }

            if (count($post['setting_id']) < 1) {
                $errors ['setting_id'] = '请选择敏感期';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}