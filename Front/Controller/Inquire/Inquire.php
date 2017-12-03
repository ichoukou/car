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

        $this->data['info'] = WC::http_request("http://v.juhe.cn/wzpoints/query?key=75f3c3f91774a7ad6da7fb60be4f30ed&lat=31.335005&lon=120.617183&r=2000");

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
//                $titles = [
//                    'name' => '加油站名称',
//                    'type' => '加油站类型',
//                    'address' => '加油站地址',
//                    'area' => '城市邮编',
//                    'brandname' => '运营商类型',
//                    'discount' => '是否打折加油站',
//                    'distance' => '与当前位置的距离（米）',
//                    'exhaust' => '尾气排放标准',
//                    'fwlsmc' => '加油卡信息',
//                    'price' => '省控基准油价',
//                ];

//                $result = [];
//                foreach ($info['result']['data'] as $k=>$v) {
//                    foreach ($v as $k1=>$v1) {
//                        if (!empty($titles[$k1])) {
//                            if ($k1 == 'price') {
//                                $petrol = [];
//                                foreach ($v1 as $k2=>$v2) {
//                                    #$petrol[$k2]['类型'] = $v2['type'];
//                                    #$petrol[$k2]['价格'] = $v2['price'];
//                                    $petrol[$k2][$v2['type']] = $v2['price'];
//                                }
//                                $result[$k][$titles[$k1]] = $petrol;
//                            } else {
//                                $result[$k][$titles[$k1]] = $v1;
//                            }
//                        }
//                    }
//                }

                exit(json_encode(['status'=>1, 'result'=>$info['result']['data']], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'没有找到相关数据'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function ajax_get_accident()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $info =  WC::http_request("http://v.juhe.cn/wzpoints/query?key=75f3c3f91774a7ad6da7fb60be4f30ed&lat={$_POST['y']}&lon={$_POST['x']}&pagesize=50&r=2000");

            if ($info['reason'] == '成功') {
                exit(json_encode(['status'=>1, 'result'=>$info['result']['list']], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'没有找到相关数据'], JSON_UNESCAPED_UNICODE));
            }
        }
    }
}