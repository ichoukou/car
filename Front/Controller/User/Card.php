<?php
namespace Admin\Controller\User;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Card extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'card_type']);
        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['cards'] = M::Admin('User\\Card', 'findCards', ['params'=>$param]);
        $count = M::Admin('User\\Card', 'findCardsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/User/Card&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/User/Card";

        $this->create_page();

        L::output(L::view('User\\CardIndex', 'Admin', $this->data));
    }

    public function add_card()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'card_type']);

        $this->data['today'] = date('Y-m-d', time());

        $this->create_page();

        L::output(L::view('User\\CardAdd', 'Admin', $this->data));
    }

    public function do_add_card()
    {
        if ($post = $this->validate_default()) {
            $user_id = M::Admin('User\\Card', 'addCard', ['post'=>$post]);

            if ($user_id > 0) {
                setcookie('success_info', '新增会员卡种信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增会员卡种信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'新增会员卡种信息失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_card()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['user_card_id']);

        $user_card_id = (int)$_GET['user_card_id'];
        if (empty($user_card_id))
            exit(header("location:{$this->data['entrance']}route=Admin/User/Card{$this->data['url']}"));

        $user_card_info = M::Admin('User\\Card', 'findUserCardByUserCardId', ['user_card_id'=>$user_card_id]);
        if (empty($user_card_info))
            exit(header("location:{$this->data['entrance']}route=Admin/User/Card{$this->data['url']}"));

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'card_type']);

        $this->data['user_card_info'] = $user_card_info;

        $this->create_page();

        L::output(L::view('User\\CardEdit', 'Admin', $this->data));
    }

    public function do_edit_card()
    {
        $this->is_login();

        if ($post = $this->validate_edit()) {
            M::Admin('User\\Card', 'editCard', ['post'=>$post]);

            setcookie('success_info', '修改会员卡种信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改会员卡种信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $user_card_id = (int)$_POST['user_card_id'];

        if (empty($user_card_id))
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少用户卡种标示'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('User\\Card', 'removeCard', ['user_card_id'=>$user_card_id]);

        if ($return) {
            setcookie('success_info', '删除会员卡种信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除会员卡种信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除会员卡种信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $user_card_ids = $_POST['user_card_ids'];

        if (count($user_card_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少用户卡种标示'], JSON_UNESCAPED_UNICODE));


        $return = M::Admin('User\\Card', 'removeCards', ['user_card_ids'=>$user_card_ids]);

        if ($return) {
            setcookie('success_info', '删除多个会员卡种信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个会员卡种信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个会员卡种信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function find_card_info()
    {
        $this->is_login();

        $setting_id = (int)$_GET['setting_id'];

        if (empty($setting_id))
            $errors ['setting_id'] = '缺少卡种标识';

        $data = [
            'module_key'    => 'setting_user_card_type_valid_period',
            'setting_id'  => $setting_id
        ];

        $result = M::Admin('User\\Card', 'findCardTypes', $data);

        if (!empty($result)) {
            exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>[]], JSON_UNESCAPED_UNICODE));
        }
    }

    public function autocomplete_baby()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = [];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $return = M::Admin('User\\Card', 'autocompleteFindBabys', ['post'=>$param]);

        $result = '';
        if (!empty($return)) {
            foreach ($return as $key=>$baby) {
                $result[$baby['baby_id']] = $baby;
            }
        }

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['baby_id'])) {
                $errors ['baby_id'] = '请选择会员宝宝';
            }

            if (empty($post['setting_id'])) {
                $errors ['setting_id'] = '请选择卡种';
            }

            if (empty($post['card_start_time']) or !c::check_date_format($post['card_start_time'])) {
                $errors ['card_start_time'] = '办卡时间不能为空,且格式正确';
            }

            if (empty($post['card_end_time']) or !c::check_date_format($post['card_end_time'])) {
                $errors ['card_end_time'] = '到期时间不能为空,且格式正确';
            }

            $post['money'] = (float)$post['money'];
            if (!is_numeric($post['money']) and !is_float($post['money'])) {
                $errors ['money'] = '办卡费用必须为纯数字或者浮点数';
            }

//            if (!isset($post['remaining_times'])) {
//                $errors ['remaining_times'] = '请填写卡种次数';
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

            if (empty($post['user_card_id'])) {
                $errors ['other_error'] = '缺少会员卡种标识';
            }

            $post['money'] = (float)$post['money'];
            if (!is_numeric($post['money']) and !is_float($post['money'])) {
                $errors ['money'] = '办卡费用必须为纯数字或者浮点数';
            }

//            if (empty($post['setting_id'])) {
//                $errors ['setting_id'] = '请选择卡种';
//            }
//
//            if (empty($post['card_start_time'])) {
//                $errors ['card_start_time'] = '请选择办卡时间';
//            }
//
//            if (empty($post['card_end_time'])) {
//                $errors ['card_end_time'] = '到期时间不能为空';
//            }

//            if (!isset($post['remaining_times'])) {
//                $errors ['remaining_times'] = '请填写卡种次数';
//            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}