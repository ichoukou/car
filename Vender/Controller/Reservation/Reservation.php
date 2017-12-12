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

        if (!empty($_COOKIE['info_error'])) {
            $this->data['info_error'] = $_COOKIE['info_error'];
            setcookie('info_error', '', time()+60);
        }

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

            exit(header("location:{$this->data['entrance']}route=Vender/Reservation/Reservation"));
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

            $header = "location:{$this->data['entrance']}route=Vender/Reservation/Reservation/edit_reservation_step2&reservation_id={$_POST['reservation_id']}";
            if (empty($post['reservation_id'])) {
                setcookie('info_error', '缺少结算标识', time()+60);
                exit(header($header));
            }

            if (empty($post['base64_file']) and empty($_FILES['audio']['name']) and empty($_FILES['video']['name'])) {
                #setcookie('info_error', '图片，视频，音频必须选择一项', time()+60);
                setcookie('info_error', '图片，视频必须选择一项', time()+60);
                exit(header($header));
            }

            $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

            if (!empty($_POST['base64_file'])) {
                $base64_file = explode(',', $_POST['base64_file']);

                if (empty($base64_file[1])) {
                    setcookie('info_error', '图片编码错误', time()+60);
                    exit(header($header));
                } else {
                    $ext_arr = explode(';', $base64_file[0]);
                    $ext = explode('/', $ext_arr[0]);
                    $ext = !empty($ext[1]) ? $ext[1] : 'jpg';
                    $file = base64_decode($base64_file[1]);

                    $ext = 'jpg';
                    $file_name = date('YmdHis', time()) . $orderSn . mt_rand(100000, 999999) . '.' . $ext;
                    $file_path = 'Image' . DS . 'upload' . DS . 'vender' . DS. 'other' . DS;
                    $return = file_put_contents(ROOT_PATH . $file_path . $file_name, $file);
                    $_POST['base64_file'] = '';
                    if (!$return) {
                        setcookie('info_error', '上传图片失败', time()+60);
                        exit(header($header));
                    }
                    $post['image_path'] = $file_path . $file_name;
                }
            } elseif (!empty($_FILES['audio']['name'])) {
                $ext = explode('/' ,$_FILES['audio']['type']);

                $file_name = date('YmdHis', time()) . $orderSn . mt_rand(100000, 999999) . '.mp3';
                $file_path = 'Image' . DS . 'upload' . DS . 'vender' . DS. 'audio' . DS;
                move_uploaded_file($_FILES['audio']['tmp_name'], ROOT_PATH . $file_path . $file_name);
                $post['audio_path'] = $file_path . $file_name;
                if ($ext[1] != 'mp3') {
                    $amr = ROOT_PATH . $file_path . $file_name;
                    $mp3 = ROOT_PATH . $file_path . 'new' . $file_name;

                    $command = "/usr/local/bin/ffmpeg -i $amr $mp3";
                    exec($command, $error);
                    $post['audio_path'] = $file_path . 'new' . $file_name;
                }
            } else {
                $ext = explode('.' ,$_FILES['video']['name']);
                $file_name = date('YmdHis', time()) . $orderSn . mt_rand(100000, 999999) . '.' . $ext[1];
                $file_path = 'Image' . DS . 'upload' . DS . 'vender' . DS. 'video' . DS;
                move_uploaded_file($_FILES['video']['tmp_name'], ROOT_PATH . $file_path . $file_name);
                $post['video_path'] = $file_path . $file_name;
            }

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
                $errors ['other_error'] = '合计金额必须大于0';
            }

            if (empty($post['base64_file']) and empty($_FILES['audio']['name']) and empty($_FILES['video']['name'])) {
                #$errors ['other_error'] = '图片，视频，音频必须选择一项';
                $errors ['other_error'] = '图片，视频必须选择一项';
            }

            if (!empty($_POST['base64_file'])) {
                $base64_file = explode(',', $_POST['base64_file']);

                if (empty($base64_file[1])) {
                    $errors ['other_error'] = '图片编码错误';
                } else {
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
                        $errors ['other_error'] = '上传图片失败';
                    }
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