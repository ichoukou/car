<?php
namespace Front\Controller\Inquire;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\WxCommon as WC;

class Inquire extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->create_page();

        L::output(L::view('Inquire\\InquireIndex', 'Front', $this->data));
    }

    public function traffic()
    {
        $this->is_login();

        $car_info = M::Front('Inquire\\Inquire', 'findCarByUserId');
        $prefix = mb_substr($car_info['plate_number'], 0, 1, 'utf-8');
        $number = mb_substr($car_info['plate_number'], 1, mb_strlen($car_info['plate_number'], 'utf8'), 'utf-8');

        $this->data['traffic_info'] = WC::http_request("http://api.jisuapi.com/illegal/query?appkey=cb20cb600e88994f&lsprefix={$prefix}&lsnum={$number}&lstype=02&frameno={$car_info['identification_number']}&engineno={$car_info['engine_number']}");

        if ($this->data['traffic_info'] != 0) {
            $this->data['error_info'] = $this->data['traffic_info']['msg'];
        }

        $this->data['car_info'] = $car_info; $this->data['car_info'] = $car_info;

        $this->create_page();

        L::output(L::view('Inquire\\TrafficIndex', 'Front', $this->data));
    }

    public function petrol_station()
    {
        $this->is_login();

        $this->create_page();

        L::output(L::view('Inquire\\PetrolStationIndex', 'Front', $this->data));
    }

    public function accident()
    {
        $this->is_login();

        $car_info = M::Front('Inquire\\Inquire', 'findCarByUserId');

        $this->data['info'] = WC::http_request("http://v.juhe.cn/wzpoints/query?key=8a61b60ac68ad4d3a3cad50272033d83&lat=31.335005&lon=120.617183&r=2000");

        if ($this->data['info']['reason'] != '成功') {
            $this->data['error_info'] = $this->data['info']['msg'];
        }

        $this->data['car_info'] = $car_info;

        $this->create_page();

        L::output(L::view('Inquire\\AccidentIndex', 'Front', $this->data));
    }

    public function ajax_get_petrol_station()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $info = WC::http_request("http://apis.juhe.cn/oil/local?key=a871392258943f1a51239b8b9f5b556d&lon={$_POST['x']}&lat={$_POST['y']}&format=2&r=10000");

            if ($info['resultcode'] == 200) {
                $result = [];
                foreach ($info['result']['data'] as $k=>$v) {
                    foreach ($v as $k1=>$v1) {
                        if ($k1 == 'name') $result[$k]['title'] = $v1;
                        if ($k1 == 'price') $result[$k]['price'] = $v1;
                        if ($k1 == 'address') $result[$k]['address'] = $v1;
                    }
                }

                exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'没有找到相关数据'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function ajax_get_accident()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $info =  WC::http_request("http://v.juhe.cn/wzpoints/query?key=8a61b60ac68ad4d3a3cad50272033d83&lat={$_POST['y']}&lon={$_POST['x']}&pagesize=50&r=2000");

            if ($info['reason'] == '成功') {
                $result = [];
                foreach ($info['result']['list'] as $k=>$v) {
                    $titles = [
                        'province' => '',
                        'city' => '',
                        'district' => '',
                        'address' => '',
                        'level' => '',
                        'num' => '',
                        'detail' => ''
                    ];
                    foreach ($v as $k1=>$v1) {
                        if ($k1 == 'province') $titles['province'] = $v1;
                        if ($k1 == 'city') $titles['city'] = $v1;
                        if ($k1 == 'district') $titles['district'] = $v1;
                        if ($k1 == 'address') $titles['address'] = $v1;
                        if ($k1 == 'level') $titles['level'] = $v1;
                        if ($k1 == 'num') $titles['num'] = $v1;
                        if ($k1 == 'detail') $titles['detail'] = $v1;
                    }
                    $result[$k] = $titles;
                }

                exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'没有找到相关数据'], JSON_UNESCAPED_UNICODE));
            }
        }
    }
}