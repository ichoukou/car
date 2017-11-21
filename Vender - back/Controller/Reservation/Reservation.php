<?php
namespace Vender\Controller\Reservation;

use Libs\Core\ControllerVender AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\BaiduOcr\OcrInit as Ocr;

class Reservation extends Controller
{
    public function index()
    {
        $this->is_login();

        #$this->data['settings'] = M::Vender('Common\\Common', 'getSettings', ['module'=>'user']);
        $page_config = M::Vender('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['reservations'] = M::Vender('Reservation\\Reservation', 'findReservations', ['params'=>$param]);
        $count = M::Vender('Reservation\\Reservation', 'findReservationsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Vender/Reservation/Reservation&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Vender/Reservation/Reservation";

        $this->create_page();

        L::output(L::view('Reservation\\ReservationIndex', 'Vender', $this->data));
    }

    public function edit_reservation()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['reservation_id']);

        $reservation_id = (int)$_GET['reservation_id'];
        if (empty($reservation_id))
            exit(header("location:{$this->data['entrance']}route=Vender/Reservation/Reservation{$this->data['url']}"));

        $reservation_info = M::Vender('Reservation\\Reservation', 'findReservationByReservationId', ['reservation_id'=>$reservation_id]);
        if (empty($reservation_info))
            exit(header("location:{$this->data['entrance']}route=Vender/Reservation/Reservation{$this->data['url']}"));

        #$this->data['settings'] = M::Vender('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->data['reservation_info'] = $reservation_info;

        $this->create_page();

        L::output(L::view('Reservation\\ReservationEdit', 'Vender', $this->data));
    }

    public function do_edit_reservation()
    {
        if ($post = $this->validate_edit()) {
            M::Vender('Reservation\\Reservation', 'editReservation', ['post'=>$post]);

            setcookie('success_info', '修改预约信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改预约信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_edit()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['reservation_id'])) {
                $errors ['other_error'] = '缺少预约标识';
            }

            if (empty($post['image_path'])) {
                $errors ['image_path'] = '接车问诊单图片';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}