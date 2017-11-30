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

        $this->data['reservation_info'] = $reservation_info;

        $this->create_page();

        L::output(L::view('Reservation\\ReservationEdit', 'Vender', $this->data));
    }

    public function edit_reservation_step2()
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

        $this->data['reservation_info'] = $reservation_info;

        $this->create_page();

        L::output(L::view('Reservation\\ReservationEditStep2', 'Vender', $this->data));
    }

    public function settlement()
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

        $this->data['reservation_info'] = $reservation_info;

        $this->create_page();

        L::output(L::view('Reservation\\SettlementEdit', 'Vender', $this->data));
    }

    public function do_edit_reservation()
    {
        if ($post = $this->validate_edit()) {
            M::Vender('Reservation\\Reservation', 'editReservation', ['post'=>$post]);

            setcookie('success_info', '修改预约信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改预约信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_add_settlement()
    {
        if ($post = $this->validate_settlement()) {
            $return = M::Vender('Reservation\\Reservation', 'addSettlement', ['post'=>$post]);

            if ($return == -1)
                exit(json_encode(['status'=>-1, 'result'=>['other_error'=>'信息匹配错误，没有找到匹配的数据']], JSON_UNESCAPED_UNICODE));

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

            if (empty($post['base64_file'])) {
                $errors ['base64_file'] = '请上传维修结算图片';
            }

            $base64_file = explode(',', $_POST['base64_file']);
            if (empty($base64_file[1])) {
                $errors ['base64_file'] = '图片编码错误';
            }

            if (empty($errors)) {
                $ext_arr = explode(';', $base64_file[0]);
                $ext = explode('/', $ext_arr[0]);
                $ext = !empty($ext[1]) ? $ext[1] : 'jpeg';
                $file = base64_decode($base64_file[1]);

                $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
                $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
                $ext = 'jpg';
                $file_name = date('YmdHis', time()) . $orderSn . mt_rand(100000, 999999) . '.' . $ext;
                $file_path = 'Image' . DS . 'upload' . DS . 'vender' . DS. 'other' . DS;
                $return = file_put_contents(ROOT_PATH . $file_path . $file_name, $file);

                if (!$return) {
                    $errors ['base64_file'] = '上传图片失败';
                }
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            $post['image_path'] = $file_path . $file_name;

            return $post;
        } else {
            return false;
        }
    }

    public function validate_settlement()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['reservation_id'])) {
                $errors ['other_error'] = '缺少结算标识';
            }

            if (empty($post['total_revenue'])) {
                $errors ['total_revenue'] = '合计金额必须大于0';
            }

            if (empty($post['base64_file'])) {
                $errors ['base64_file'] = '请上传维修结算图片';
            }
            var_Dump('aaaaaaaaaa');
            exit;
            $base64_file = explode(',', $_POST['base64_file']);
            if (empty($base64_file[1])) {
                $errors ['base64_file'] = '图片编码错误';
            }

            if (empty($errors)) {
                $ext_arr = explode(';', $base64_file[0]);
                $ext = explode('/', $ext_arr[0]);
                $ext = !empty($ext[1]) ? $ext[1] : 'jpeg';
                $file = base64_decode($base64_file[1]);

                $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
                $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
                $ext = 'jpg';
                $file_name = date('YmdHis', time()) . $orderSn . mt_rand(100000, 999999) . '.' . $ext;
                $file_path = 'Image' . DS . 'upload' . DS . 'vender' . DS. 'other' . DS;
                $return = file_put_contents(ROOT_PATH . $file_path . $file_name, $file);

                if (!$return) {
                    $errors ['base64_file'] = '上传图片失败';
                }
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            $post['image_path'] = $file_path . $file_name;

            return $post;
        } else {
            return false;
        }
    }
}