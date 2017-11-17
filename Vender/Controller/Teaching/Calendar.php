<?php
namespace Admin\Controller\Teaching;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Calendar extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>['teaching_aids', 'baby_sensitive_period', 'card_type']]);

        $default_param = [];

        $param = C::make_filter($_GET, $default_param);
        $this->data['url'] = C::create_url($param);

        #$this->data['teachings'] = M::Admin('Teaching\\Calendar', 'findTeachings', ['params'=>$param]);
        #$count = M::Admin('Teaching\\Calendar', 'findTeachingsCount', ['params'=>$param]);

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/Teaching/Calendar";

        $this->create_page();

        L::output(L::view('Teaching\\CalendarIndex', 'Admin', $this->data));
    }

    public function ajax_get_list()
    {
        $this->is_login();

        $teachings = M::Admin('Teaching\\Calendar', 'ajaxFindTeachingsList', ['params'=>$_POST]);

        $results = [];
        if (!empty($teachings)) {
            foreach ($teachings as $t) {
                $d = explode('-', $t['teaching_date']);
                $results[$d[2]][] = $t['teaching_id'];
            }
        }

        exit(json_encode(['status'=>1, 'result'=>$results], JSON_UNESCAPED_UNICODE));
    }

    public function ajax_get_list_info()
    {
        $this->is_login();

        $teachings = M::Admin('Teaching\\Calendar', 'ajaxFindTeachingsListInfo', ['params'=>$_POST]);

        exit(json_encode(['status'=>1, 'result'=>$teachings], JSON_UNESCAPED_UNICODE));
    }

    public function add_teaching()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>['teaching_aids', 'teaching_times', 'baby_sensitive_period', 'card_type']]);

        $this->data['today'] = date('Y-m-d', time());

        $this->create_page();

        L::output(L::view('Teaching\\TeachingAdd', 'Admin', $this->data));
    }

    public function do_add_teaching()
    {
        if ($post = $this->validate_default()) {

            $teaching_id = M::Admin('Teaching\\Teaching', 'addTeaching', ['post'=>$post]);

            if ($teaching_id > 0) {
                setcookie('success_info', '新增会员上课信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增会员上课信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                if ($teaching_id == -1) {
                    $errors ['other_error'] = '没有找到会员信息或者会员卡种信息';
                } elseif ($teaching_id == -2) {
                    $errors ['other_error'] = '会员卡种剩余次数不足以支付此课程所需的课时';
                } else {
                    $errors ['other_error'] = '新增会员上课信息失败';
                }
                exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_teaching()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['teaching_id']);

        $teaching_id = (int)$_GET['teaching_id'];
        if (empty($teaching_id))
            exit(header("location:{$this->data['entrance']}route=Admin/Teaching/Teaching{$this->data['url']}"));

        $teaching_info = M::Admin('Teaching\\Teaching', 'findTeachingByTeachingId', ['teaching_id'=>$teaching_id]);
        if (empty($teaching_info))
            exit(header("location:{$this->data['entrance']}route=Admin/Teaching/Teaching{$this->data['url']}"));

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>['teaching_aids', 'teaching_times', 'baby_sensitive_period', 'card_type']]);

        $this->data['teaching_info'] = $teaching_info;

        $this->create_page();

        L::output(L::view('Teaching\\TeachingEdit', 'Admin', $this->data));
    }

    public function do_edit_teaching()
    {
        if ($post = $this->validate_edit()) {
            M::Admin('Teaching\\Teaching', 'editTeaching', ['post'=>$post]);

            setcookie('success_info', '编辑会员上课信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'编辑会员上课信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function autocomplete_user()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = [];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $return = M::Admin('Teaching\\Teaching', 'autocompleteFindUsers', ['post'=>$param]);

        $result = '';
        if (!empty($return)) {
            foreach ($return as $key=>$user) {
                $result[$user['user_id']] = $user;
            }
        }

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function autocomplete_baby()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = [];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $return = M::Admin('Teaching\\Teaching', 'autocompleteFindBabys', ['post'=>$param]);

        $result = '';
        if (!empty($return)) {
            foreach ($return as $key=>$baby) {
                $result[$baby['baby_id']] = $baby;
            }
        }

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function autocomplete_sensitive_period()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = ['module'=>'baby_sensitive_period'];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $return = M::Admin('Teaching\\Teaching', 'autocompleteFindSensitivePeriodSettings', ['post'=>$param]);

        $result = '';
        if (!empty($return)) {
            foreach ($return as $key=>$sensitive_period) {
                $result[$sensitive_period['setting_id']] = $sensitive_period;
            }
        }

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function autocomplete_teaching_tools()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = ['module'=>'teaching_aids','key'=>'setting_teaching_aids'];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $return = M::Admin('Teaching\\Teaching', 'autocompleteFindTeachingToolsSettings', ['post'=>$param]);

        $result = '';
        if (!empty($return)) {
            foreach ($return as $key=>$tool) {
                $result[$tool['setting_id']] = $tool;
            }
        }

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function remove_one()
    {
        $this->is_login();

        $teaching_id = (int)$_POST['teaching_id'];

        if (empty($teaching_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少课程标示'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Teaching\\Teaching', 'removeTeaching', ['teaching_id'=>$teaching_id]);

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

        $teaching_ids = $_POST['teaching_ids'];

        if (count($teaching_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少课程标示'], JSON_UNESCAPED_UNICODE));


        $return = M::Admin('Teaching\\Teaching', 'removeTeachings', ['teaching_ids'=>$teaching_ids]);

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

            if (empty($post['baby_id'])) {
                $errors ['baby_id'] = '请选择宝宝和父母信息';
            }

            if (empty($post['user_card_id'])) {
                $errors ['user_card_id'] = '请选择会员卡种';
            }

            if (empty($post['setting_id'])) {
                $errors ['setting_id'] = '请选择敏感期';
            }

            if (empty($post['teaching_tool_setting_id'])) {
                $errors ['teaching_tool_setting_id'] = '请选择教具';
            }

//            if (empty($post['title'])) {
//                $errors ['title'] = '请填写课程名称';
//            }

            if (!c::check_date_format($post['teaching_date'])) {
                $errors ['teaching_date'] = '请选择上课日期';
            }

             if (!c::check_time_format($post['teaching_start_time'])) {
                $errors ['teaching_start_time'] = '请选择上课开始时间';
            }

            if (!c::check_time_format($post['teaching_end_time'])) {
                $errors ['teaching_end_time'] = '请选择上课结束时间';
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

            if (empty($post['teaching_id'])) {
                $errors ['other_error'] = '缺少课程标识';
            }

            if (empty($post['teaching_tool_setting_id'])) {
                $errors ['teaching_tool_setting_id'] = '请选择教具';
            }

//            if (empty($post['title'])) {
//                $errors ['title'] = '请填写课程名称';
//            }

            if (!c::check_date_format($post['teaching_date'])) {
                $errors ['teaching_date'] = '请选择上课日期';
            }

            if (!c::check_time_format($post['teaching_start_time'])) {
                $errors ['teaching_start_time'] = '请选择上课开始时间';
            }

            if (!c::check_time_format($post['teaching_end_time'])) {
                $errors ['teaching_end_time'] = '请选择上课结束时间';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}