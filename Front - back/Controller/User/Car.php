<?php
namespace Front\Controller\User;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Car extends Controller
{
    public function index()
    {
        $this->is_login();

        #$this->data['settings'] = M::Front('Common\\Common', 'getSettings', ['module'=>'user']);
        $page_config = M::Front('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['cars'] = M::Front('User\\Car', 'findCars', ['params'=>$param]);
        $count = M::Front('User\\Car', 'findCarsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Front/User/Car&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Front/User/Car";

        $this->create_page();

        L::output(L::view('User\\CarIndex', 'Front', $this->data));
    }

    public function add_car()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        #$this->data['settings'] = M::Front('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->create_page();

        L::output(L::view('User\\CarAdd', 'Front', $this->data));
    }

    public function do_add_car()
    {
        if ($post = $this->validate_default()) {
            $car_id = M::Front('User\\Car', 'addCar', ['post'=>$post]);

            if ($car_id > 0) {
                setcookie('success_info', '新增车辆信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增车辆信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'新增车辆信息失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_car()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['car_id']);

        $car_id = (int)$_GET['car_id'];
        if (empty($car_id))
            exit(header("location:{$this->data['entrance']}route=Front/User/Car{$this->data['url']}"));

        $car_info = M::Front('User\\Car', 'findCarByCarId', ['car_id'=>$car_id]);
        if (empty($car_info))
            exit(header("location:{$this->data['entrance']}route=Front/User/Car{$this->data['url']}"));

        #$this->data['settings'] = M::Front('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->data['car_info'] = $car_info;

        $this->create_page();

        L::output(L::view('User\\CarEdit', 'Front', $this->data));
    }

    public function do_edit_car()
    {
        if ($post = $this->validate_edit()) {
            $return = M::Front('User\\Car', 'editCar', ['post'=>$post]);
            if ($return == -1)
                exit(json_encode(['status'=>-1, 'result'=>'车辆信息匹配错误'], JSON_UNESCAPED_UNICODE));

            setcookie('success_info', '修改车辆信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改车辆信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $car_id = (int)$_POST['car_id'];

        if (empty($car_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少车辆标识'], JSON_UNESCAPED_UNICODE));

        $return = M::Front('User\\Car', 'removeCar', ['car_id'=>$car_id]);

        if ($return) {
            setcookie('success_info', '删除车辆信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除车辆信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除车辆信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $car_ids = $_POST['car_ids'];

        if (count($car_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少车辆标识'], JSON_UNESCAPED_UNICODE));

        $return = M::Front('User\\Car', 'removeCars', ['car_ids'=>$car_ids]);

        if ($return) {
            setcookie('success_info', '删除多个车辆信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个车辆信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个车辆信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_default()
    {
        $this->is_login();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['plate_number'])) {
                $errors ['plate_number'] = '请填写号牌号码';
            }

            if (empty($post['car_type'])) {
                $errors ['car_type'] = '请填写车辆类型';
            }

            if (empty($post['owner'])) {
                $errors ['owner'] = '请填写所有人';
            }

            if (empty($post['address'])) {
                $errors ['address'] = '请填写住址';
            }

            if (empty($post['use_type'])) {
                $errors ['use_type'] = '请填写使用性质';
            }

            if (empty($post['brand_type'])) {
                $errors ['brand_type'] = '请填写品牌型号';
            }

            if (empty($post['identification_number'])) {
                $errors ['identification_number'] = '请填写车辆识别代号';
            }

            if (empty($post['engine_number'])) {
                $errors ['engine_number'] = '请填写发动机号码';
            }

            if (empty($post['registration_date'])) {
                $errors ['registration_date'] = '请填写注册日期';
            }

            if (empty($post['accepted_date'])) {
                $errors ['accepted_date'] = '请填写受理日期';
            }

            if (empty($post['file_number'])) {
                $errors ['file_number'] = '请填写档案编号';
            }

            if (empty($post['people_number'])) {
                $errors ['people_number'] = '请填写核定人数';
            }

            if (empty($post['total_mass'])) {
                $errors ['total_mass'] = '请填写总质量';
            }

            if (empty($post['dimension'])) {
                $errors ['dimension'] = '请填写外观尺寸';
            }

            if (empty($post['description'])) {
                $errors ['description'] = '请填写备注';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }

    public function validate_edit()
    {
        $this->is_login();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['car_id'])) {
                $errors ['other_error'] = '缺少车辆标识';
            }

            if (empty($post['plate_number'])) {
                $errors ['plate_number'] = '请填写号牌号码';
            }

            if (empty($post['car_type'])) {
                $errors ['car_type'] = '请填写车辆类型';
            }

            if (empty($post['owner'])) {
                $errors ['owner'] = '请填写所有人';
            }

            if (empty($post['address'])) {
                $errors ['address'] = '请填写住址';
            }

            if (empty($post['use_type'])) {
                $errors ['use_type'] = '请填写使用性质';
            }

            if (empty($post['brand_type'])) {
                $errors ['brand_type'] = '请填写品牌型号';
            }

            if (empty($post['identification_number'])) {
                $errors ['identification_number'] = '请填写车辆识别代号';
            }

            if (empty($post['engine_number'])) {
                $errors ['engine_number'] = '请填写发动机号码';
            }

            if (empty($post['registration_date'])) {
                $errors ['registration_date'] = '请填写注册日期';
            }

            if (empty($post['accepted_date'])) {
                $errors ['accepted_date'] = '请填写受理日期';
            }

            if (empty($post['file_number'])) {
                $errors ['file_number'] = '请填写档案编号';
            }

            if (empty($post['people_number'])) {
                $errors ['people_number'] = '请填写核定人数';
            }

            if (empty($post['total_mass'])) {
                $errors ['total_mass'] = '请填写总质量';
            }

            if (empty($post['dimension'])) {
                $errors ['dimension'] = '请填写外观尺寸';
            }

            if (empty($post['description'])) {
                $errors ['description'] = '请填写备注';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}