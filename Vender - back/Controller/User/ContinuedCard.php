<?php
namespace Admin\Controller\User;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class ContinuedCard extends Controller
{
    public function index()
    {
        $this->is_login();

        $validate_return = $this->validate_user_card();

        if ($validate_return == -1) {
            setcookie('error_info', '缺少会员卡种信息标识', time() + 60);
            exit(header("location:{$this->data['entrance']}route=Admin/User/Card"));
        } elseif($validate_return == -2) {
            setcookie('error_info', '没有找到会员卡种信息', time() + 60);
            exit(header("location:{$this->data['entrance']}route=Admin/User/Card"));
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

        $this->data['continued_cards'] = M::Admin('User\\ContinuedCard', 'findContinuedCards', ['params'=>$param]);
        $count = M::Admin('User\\ContinuedCard', 'findContinuedCardsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/User/ContinuedCard&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/User/ContinuedCard&user_card_id={$this->data['user_card_info']['user_card_id']}";

        $this->create_page();

        L::output(L::view('User\\ContinuedCardIndex', 'Admin', $this->data));
    }

    public function add_continued_card()
    {
        $this->is_login();

        $validate_return = $this->validate_user_card();

        if ($validate_return == -1) {
            exit('缺少用户卡种信息标识');
        } elseif($validate_return == -2) {
            exit('没有找到用户卡种信息标识');
        }

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'card_type']);

        $this->create_page();

        L::output(L::view('User\\ContinuedCardAdd', 'Admin', $this->data));
    }

    public function do_add_continued_card()
    {
        if ($post = $this->validate_default()) {
            $continued_card_id = M::Admin('User\\ContinuedCard', 'addContinuedCard', ['post'=>$post]);

            if ($continued_card_id > 0) {
                setcookie('success_info', '新增续卡信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增续卡信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'新增续卡信息失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_continued_card()
    {
        $this->is_login();

        $this->validate_user_card();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['user_continued_card_id']);

        $user_continued_card_id = (int)$_GET['user_continued_card_id'];
        if (empty($user_continued_card_id))
            exit(header("location:{$this->data['entrance']}route=Admin/User/ContinuedCard{$this->data['url']}"));

        $user_continued_card_info = M::Admin('User\\ContinuedCard', 'findUserContinuedCardByUserContinuedCardId', ['user_continued_card_id'=>$user_continued_card_id]);
        if (empty($user_continued_card_info))
            exit(header("location:{$this->data['entrance']}route=Admin/User/ContinuedCard{$this->data['url']}"));

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'card_type']);

        $this->data['user_continued_card_info'] = $user_continued_card_info;

        $this->create_page();

        L::output(L::view('User\\ContinuedCardEdit', 'Admin', $this->data));
    }

    public function do_edit_continued_card()
    {
        if ($post = $this->validate_edit()) {
            M::Admin('User\\ContinuedCard', 'editContinuedCard', ['post'=>$post]);

            setcookie('success_info', '修改会员续卡信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改会员续卡信息成功'], JSON_UNESCAPED_UNICODE));
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

    public function find_card_valid_period_info()
    {
        $setting_id = (int)$_GET['setting_id'];

        if (empty($setting_id))
            $errors ['setting_id'] = '缺少卡种标识';

        $data = [
            'setting_id'  => $setting_id
        ];

        $result = M::Admin('User\\ContinuedCard', 'findCardValidPeriods', $data);

        if (!empty($result)) {
            exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>[]], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_user_card($user_card_id = null)
    {
        $user_card_id = !empty($user_card_id) ? (int)$user_card_id :(int)$_GET['user_card_id'];
        if (empty($user_card_id)) {
            return -1;
        } else {
            $this->data['user_card_info'] = M::Admin('User\\ContinuedCard', 'findUserCardByUserCardId', ['user_card_id'=>$user_card_id]);

            if (empty($this->data['user_card_info'])) {
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

            $post['user_card_id'] = (int)$post['user_card_id'];
            if (empty($post['user_card_id'])) {
                $errors ['other_error'] = '缺少续卡标识';
            }

            $post['valid_period_setting_id'] = (int)$post['valid_period_setting_id'];
            if (empty($post['valid_period_setting_id'])) {
                $errors ['valid_period_setting_id'] = '请选择续卡时间';
            }

            if (empty($post['card_end_time']) or !c::check_date_format($post['card_end_time'])) {
                $errors ['card_end_time'] = '到期时间不能为空,且格式正确';
            }

            $post['money'] = (float)$post['money'];
            if (!is_numeric($post['money']) and !is_float($post['money'])) {
                $errors ['money'] = '办卡费用必须为纯数字或者浮点数';
            }

//            $remaining_times = (int)$post['remaining_times'];
//            if (!isset($remaining_times)) {
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

            $post['user_continued_card_id'] = (int)$post['user_continued_card_id'];
            if (empty($post['user_continued_card_id'])) {
                $errors ['other_error'] = '缺少续卡标识';
            }

            $post['money'] = (float)$post['money'];
            if (!is_numeric($post['money']) and !is_float($post['money'])) {
                $errors ['money'] = '办卡费用必须为纯数字或者浮点数';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}