<?php
namespace Front\Controller\User;

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

        $this->data['reservations'] = M::Front('User\\Reservation', 'findReservations', ['params'=>$param]);
        $count = M::Front('User\\Reservation', 'findReservationsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Front/User/Reservation&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Front/User/Reservation";

        $this->create_page();

        L::output(L::view('User\\ReservationIndex', 'Front', $this->data));
    }

    public function edit_reservation()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['reservation_id']);

        $reservation_id = (int)$_GET['reservation_id'];
        if (empty($reservation_id))
            exit(header("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}"));

        $info = M::Front('User\\Reservation', 'findReservationByReservationId', ['reservation_id'=>$reservation_id]);
        if (empty($info))
            exit(header("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}"));

        #$this->data['settings'] = M::Front('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->data['reservation_info'] = $info;

        if (empty($info['score']) and empty($info['score_count'])) {
            $evaluation = '暂无评价';
        } else {
            $this->data['score'] = round($info['score'] / $info['score_count']);
            if ($this->data['score'] > 5) {
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

        L::output(L::view('User\\ReservationEdit', 'Front', $this->data));
    }

    public function do_edit_reservation()
    {
        if ($post = $this->validate_edit()) {
            M::Front('User\\Reservation', 'editReservation', ['post'=>$post]);

            setcookie('success_info', '修改预约信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改预约信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $company_id = (int)$_POST['company_id'];
        if (empty($company_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少用户标识'], JSON_UNESCAPED_UNICODE));

        $info = M::Front('Company\\Company', 'findCompanyByCompanyId', ['company_id'=>$company_id]);
        if (empty($info))
            exit(json_encode(['status'=>1, 'result'=>'没有找到要删除的信息'], JSON_UNESCAPED_UNICODE));

        $company_info = M::Front('Company\\Company', 'findCompanyBySession');
        if ($company_info['pid'] == 0) {
            if ($company_info['company_id'] == $company_id) {
                exit(json_encode(['status'=>-1, 'result'=>'删除失败,无法删除主账号'], JSON_UNESCAPED_UNICODE));
            } elseif ($company_info['company_id'] != $info['pid'] and $company_info['company_id'] != $info['company_id']) { #主账户无权修改其他主账户或其他主账户下的子账户
                exit(json_encode(['status'=>1, 'result'=>'无权限删除'], JSON_UNESCAPED_UNICODE));
            }
        } elseif($company_info['pid'] != 0 and $company_info['company_id'] == $company_id) {
            exit(json_encode(['status'=>-1, 'result'=>'不能删除自己的账号'], JSON_UNESCAPED_UNICODE));
        }

        $return = M::Front('Company\\Company', 'removeCompany', ['company_id'=>$company_id]);
        if ($return) {
            setcookie('success_info', '删除企业信息成功', time() + 60);
            exit(json_encode(['status'=>1, 'result'=>'删除企业信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除企业信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $company_ids = $_POST['company_ids'];
        if (count($company_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少用户标识'], JSON_UNESCAPED_UNICODE));

        $return = M::Front('Company\\Company', 'removeCompanys', ['company_ids'=>$company_ids]);
        if ($return) {
            setcookie('success_info', '删除多个企业信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个企业信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个企业信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_edit()
    {
        $this->is_login();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['reservation_id'])) {
                $errors ['other_error'] = '缺少预约信息标识';
            } else {
                $info = M::Front('User\\Reservation', 'findReservationByReservationId', ['reservation_id'=>$post['reservation_id']]);
                if (empty($info)) {
                    $errors ['other_error'] = '没有找到预约信息';
                }
            }

            if (empty($post['reservation_time'])) {
                $errors ['reservation_time'] = '请选择预约时间';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}