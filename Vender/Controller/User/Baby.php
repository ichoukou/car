<?php
namespace Admin\Controller\User;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Baby extends Controller
{
    public function index()
    {
        $this->is_login();

        $validate_return = $this->validate_parents();

        if ($validate_return == -1) {
            setcookie('error_info', '缺少父母信息标识', time() + 60);
            exit(header("location:{$this->data['entrance']}route=Admin/User/User"));
        } elseif($validate_return == -2) {
            setcookie('error_info', '没有找到父母信息', time() + 60);
            exit(header("location:{$this->data['entrance']}route=Admin/User/User"));
        }

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'baby']);
        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['babys'] = M::Admin('User\\Baby', 'findBabys', ['params'=>$param]);
        $count = M::Admin('User\\Baby', 'findBabysCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/User/Baby&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/User/Baby&user_id={$_GET['user_id']}";

        $this->create_page();

        L::output(L::view('User\\BabyIndex', 'Admin', $this->data));
    }

    public function add_baby()
    {
        $this->is_login();

        $validate_return = $this->validate_parents();

        if ($validate_return == -1) {
            exit('缺少父母信息标识');
        } elseif($validate_return == -2) {
            exit('没有找到父母信息');
        }

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'baby']);

        $this->create_page();

        L::output(L::view('User\\BabyAdd', 'Admin', $this->data));
    }

    public function do_add_baby()
    {
        if ($post = $this->validate_default()) {
            $baby_id = M::Admin('User\\Baby', 'addBaby', ['post'=>$post]);

            if ($baby_id > 0) {
                setcookie('success_info', '新增会员宝宝信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增会员宝宝信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>['other_error'=>'新增会员宝宝信息失败']], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_baby()
    {
        $this->is_login();

        $this->validate_parents();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['baby_id']);

        $baby_id = (int)$_GET['baby_id'];
        if (empty($baby_id))
            exit(header("location:{$this->data['entrance']}route=Admin/User/Baby{$this->data['url']}"));

        $baby_info = M::Admin('User\\Baby', 'findBabyByBabyId', ['baby_id'=>$baby_id]);
        if (empty($baby_info))
            exit(header("location:{$this->data['entrance']}route=Admin/User/Baby{$this->data['url']}"));

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'baby']);

        $this->data['baby_info'] = $baby_info;

        $this->create_page();

        L::output(L::view('User\\BabyEdit', 'Admin', $this->data));
    }

    public function do_edit_baby()
    {
        if ($post = $this->validate_edit()) {
            M::Admin('User\\Baby', 'editBaby', ['post'=>$post]);

            setcookie('success_info', '修改会员宝宝信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改会员宝宝信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $baby_id = (int)$_POST['baby_id'];

        if (empty($baby_id))
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少用户标示'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('User\\Baby', 'removeBaby', ['baby_id'=>$baby_id]);

        if ($return) {
            setcookie('success_info', '删除会员宝宝信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除会员宝宝信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除会员宝宝信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $baby_ids = $_POST['baby_ids'];

        if (count($baby_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'修改失败,缺少用户标示'], JSON_UNESCAPED_UNICODE));


        $return = M::Admin('User\\Baby', 'removeBabys', ['baby_ids'=>$baby_ids]);

        if ($return) {
            setcookie('success_info', '删除多个会员宝宝信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个会员宝宝信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个会员宝宝信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function autocomplete_baby_character()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = ['module'=>'baby','key'=>'setting_userbaby_baby_character'];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $return = M::Admin('User\\Baby', 'autocompleteFindBabyCharacter', ['post'=>$param]);

        $result = '';
        if (!empty($return)) {
            foreach ($return as $key=>$tool) {
                $result[$tool['setting_id']] = $tool;
            }
        }

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function validate_parents($user_id = null)
    {
        $user_id = !empty($user_id) ? (int)$user_id :(int)$_GET['user_id'];
        if (empty($user_id)) {
            return -1;
        } else {
            $this->data['user_info'] = M::Admin('User\\User', 'findUserByUserId', ['user_id'=>$user_id]);

            if (empty($this->data['user_info'])) {
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

            $user_id = (int)$post['user_id'];

            $validate_return = $this->validate_parents($user_id);

            if ($validate_return == -1) {
                $errors ['other_error'] = '缺少父母信息标识';
            } elseif($validate_return == -2) {
                $errors ['other_error'] = '没有找到父母信息';
            }

            if (empty($post['name'])) {
                $errors ['name'] = '请填写宝宝姓名';
            }

            if (!isset($post['age'])) {
                $errors ['age'] = '请填写宝宝年龄';
            }

            if (empty($post['sex'])) {
                $errors ['sex'] = '请选择宝宝性别';
            }

            if (empty($post['birthday'])) {
                $errors ['birthday'] = '请选择出生年月';
            }

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

            if (empty($post['baby_id'])) {
                $errors ['other_error'] = '缺少宝宝信息标识';
            }

            if (empty($post['name'])) {
                $errors ['name'] = '请填写宝宝姓名';
            }

            if (!isset($post['age'])) {
                $errors ['age'] = '请填写宝宝年龄';
            }

            if (empty($post['sex'])) {
                $errors ['sex'] = '请选择宝宝性别';
            }

            if (empty($post['birthday'])) {
                $errors ['birthday'] = '请选择出生年月';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}