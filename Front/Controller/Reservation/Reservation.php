<?php
namespace Front\Controller\Reservation;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Reservation extends Controller
{
    public function index()
    {
        $this->is_login();

        #$this->data['settings'] = M::Front('Common\\Common', 'getSettings', ['module'=>'user']);
        $page_config = M::Front('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'       => (int)$page_config['paging_limit']['value'],
            'page'        => 1,
            'user_id'     => $_SESSION['user_id']
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param, ['user_id']);

        $this->data['reservations'] = M::Front('Reservation\\Reservation', 'findReservations', ['params'=>$param]);
        $count = M::Front('Reservation\\Reservation', 'findReservationsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Front/Reservation/Reservation&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['car_info'] = M::Front('User\\Car', 'findCarBUserId');

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Front/Reservation/Reservation";

        $this->create_page();

        L::output(L::view('Reservation\\ReservationIndex', 'Front', $this->data));
    }

    public function company_details()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['company_id']);

        $company_id = (int)$_GET['company_id'];
        if (empty($company_id))
            exit(header("location:{$this->data['entrance']}route=Front/Reservation/Reservation{$this->data['url']}"));

        $info = M::Front('Reservation\\Reservation', 'findReservationByCompanyId', ['company_id'=>$company_id]);
        if (empty($info))
            exit(header("location:{$this->data['entrance']}route=Front/Reservation/Reservation{$this->data['url']}"));

        $detail_info = M::Front('Reservation\\Reservation', 'findCompanyDetailsByCompanyId', ['company_id'=>$company_id, 'name'=>$info['name']]);

        $this->data['info'] = $detail_info;

        $this->create_page();

        L::output(L::view('Reservation\\CompanyDetail', 'Front', $this->data));
    }

    public function add_reservation()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['company_id']);

        $company_id = (int)$_GET['company_id'];
        if (empty($company_id))
            exit(header("location:{$this->data['entrance']}route=Front/Reservation/Reservation{$this->data['url']}"));

        $info = M::Front('Reservation\\Reservation', 'findReservationByCompanyId', ['company_id'=>$company_id]);
        if (empty($info))
            exit(header("location:{$this->data['entrance']}route=Front/Reservation/Reservation{$this->data['url']}"));

        #$this->data['settings'] = M::Front('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->data['company_info'] = $info;

        if (empty($info['score']) and empty($info['score_count'])) {
            $evaluation = '暂无评价';
        } else {
            $this->data['score'] = round($info['score'] / $info['score_count']);
            if ($this->data['score'] >= 5) {
                $evaluation = '非常好';
            } elseif($this->data['score'] >= 4 and $this->data['score'] < 5) {
                $evaluation = '很好';
            } elseif($this->data['score'] >= 3 and $this->data['score'] < 4) {
                $evaluation = '良好';
            } elseif($this->data['score'] >= 2 and $this->data['score'] < 3) {
                $evaluation = '一般';
            } else {
                $evaluation = '不好';
            }
        }

        $this->data['evaluation'] = $evaluation;

        $this->create_page();

        L::output(L::view('Reservation\\ReservationAdd', 'Front', $this->data));
    }

    public function do_add_reservation()
    {
        if ($post = $this->validate_default()) {
            $reservation = M::Front('Reservation\\Reservation', 'addReservation', ['post'=>$post]);

            if ($reservation > 0) {
                setcookie('success_info', '新增预约成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增预约成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'新增预约失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function validate_default()
    {
        $this->is_login();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['company_id'])) {
                $errors ['other_error'] = '缺少企业标识';
            } else {
                $info = M::Front('Reservation\\Reservation', 'findReservationByCompanyId', ['company_id'=>$post['company_id']]);
                if (empty($info)) {
                    $errors ['other_error'] = '没有找到企业信息';
                }
            }

            $reservation_time = explode(' ', $post['reservation_time']);
            if (!C::check_date_format($reservation_time[0])) {
                $errors ['reservation_time'] = '请选择预约时间';
            }

            if (empty($post['description'])) {
                $errors ['reservation_time'] = '请填写描述';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}