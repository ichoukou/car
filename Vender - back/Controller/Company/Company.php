<?php
namespace Vender\Controller\Company;

use Libs\Core\ControllerVender AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Company extends Controller
{
    public function index()
    {
        $this->is_login();

        #$this->data['settings'] = M::Vender('Common\\Common', 'getSettings', ['module'=>'user']);
        $page_config = M::Vender('Common\\Common', 'getConfigs', ['module'=>'page']);
        $this->data['company_info'] = M::Vender('Company\\Company', 'findCompanyBySession');

        $default_param = [
            'limit'       => (int)$page_config['paging_limit']['value'],
            'page'        => 1,
            'company_id'  => $_SESSION['company_id']
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param, ['company_id']);

        $this->data['companys'] = M::Vender('Company\\Company', 'findCompanys', ['params'=>$param]);
        $count = M::Vender('Company\\Company', 'findCompanysCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Vender/Company/Company&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Vender/Company/Company";

        $this->create_page();

        L::output(L::view('Company\\CompanyIndex', 'Vender', $this->data));
    }

    public function add_company()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $company_info = M::Vender('Company\\Company', 'findCompanyBySession');
        if ($company_info['pid'] != 0)
            exit(header("location:{$this->data['entrance']}route=Vender/Company/Company{$this->data['url']}"));

        #$this->data['settings'] = M::Vender('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->create_page();

        L::output(L::view('Company\\CompanyAdd', 'Vender', $this->data));
    }

    public function do_add_company()
    {
        if ($post = $this->validate_default()) {
            $company_id = M::Vender('Company\\Company', 'addCompany', ['post'=>$post]);

            if ($company_id > 0) {
                setcookie('success_info', '新增企业信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增企业信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'电话号码被使用'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_company()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['company_id']);

        $company_id = (int)$_GET['company_id'];
        if (empty($company_id))
            exit(header("location:{$this->data['entrance']}route=Vender/Company/Company{$this->data['url']}"));

        $info = M::Vender('Company\\Company', 'findCompanyByCompanyId', ['company_id'=>$company_id]);
        if (empty($info))
            exit(header("location:{$this->data['entrance']}route=Vender/Company/Company{$this->data['url']}"));

        $company_info = M::Vender('Company\\Company', 'findCompanyBySession');
        if ($company_info['pid'] != 0 and $company_info['company_id'] != $info['company_id']) { #非主账户无权修改其他账户的
            exit(header("location:{$this->data['entrance']}route=Vender/Company/Company{$this->data['url']}"));
        } elseif($company_info['pid'] == 0 and $company_info['company_id'] != $info['pid'] and $company_info['company_id'] != $info['company_id']) { #主账户无权修改其他主账户或其他主账户下的子账户
            exit(header("location:{$this->data['entrance']}route=Vender/Company/Company{$this->data['url']}"));
        }

        #$this->data['settings'] = M::Vender('Common\\Common', 'getSettings', ['module'=>'user']);

        $this->data['company_info'] = $info;

        $this->create_page();

        L::output(L::view('Company\\CompanyEdit', 'Vender', $this->data));
    }

    public function do_edit_company()
    {
        if ($post = $this->validate_edit()) {
            M::Vender('Company\\Company', 'editCompany', ['post'=>$post]);

            setcookie('success_info', '修改企业息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改企业息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $company_id = (int)$_POST['company_id'];
        if (empty($company_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少用户标识'], JSON_UNESCAPED_UNICODE));

        $info = M::Vender('Company\\Company', 'findCompanyByCompanyId', ['company_id'=>$company_id]);
        if (empty($info))
            exit(json_encode(['status'=>1, 'result'=>'没有找到要删除的信息'], JSON_UNESCAPED_UNICODE));

        $company_info = M::Vender('Company\\Company', 'findCompanyBySession');
        if ($company_info['pid'] == 0) {
            if ($company_info['company_id'] == $company_id) {
                exit(json_encode(['status'=>-1, 'result'=>'删除失败,无法删除主账号'], JSON_UNESCAPED_UNICODE));
            } elseif ($company_info['company_id'] != $info['pid'] and $company_info['company_id'] != $info['company_id']) { #主账户无权修改其他主账户或其他主账户下的子账户
                exit(json_encode(['status'=>1, 'result'=>'无权限删除'], JSON_UNESCAPED_UNICODE));
            }
        } elseif($company_info['pid'] != 0 and $company_info['company_id'] == $company_id) {
            exit(json_encode(['status'=>-1, 'result'=>'不能删除自己的账号'], JSON_UNESCAPED_UNICODE));
        }

        $return = M::Vender('Company\\Company', 'removeCompany', ['company_id'=>$company_id]);
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

        $return = M::Vender('Company\\Company', 'removeCompanys', ['company_ids'=>$company_ids]);
        if ($return) {
            setcookie('success_info', '删除多个企业信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个企业信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个企业信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_default()
    {
        $this->is_login();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

//            if (empty($post['email']) or !C::is_email($post['email'])) {
//                $errors ['email'] = '请填写正确的邮箱';
//            } elseif(M::Admin('User\\User', 'findUserByEmail', ['email'=>$post['email']]) != '') {
//                $errors ['email'] = '此邮箱已被使用';
//            }

            $company_info = M::Vender('Company\\Company', 'findCompanyBySession');
            if ($company_info['pid'] != 0) {
                $errors ['other_error'] = '此手机号码已经被使用';
            }

            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
                $errors ['tel'] = '请填写正确的11位手机号码';
            } elseif (M::Vender('Company\\Company', 'findCompanyByTel', ['tel'=>$post['tel']]) != '') {
                $errors ['tel'] = '此手机号码已经被使用';
            }

            if (empty($post['name'])) {
                $errors ['name'] = '请填写名称';
            }

            if (empty($post['type'])) {
                $errors ['type'] = '请填写类型';
            }

            if (empty($post['address'])) {
                $errors ['address'] = '请填写住所';
            }

            if (empty($post['legal_person'])) {
                $errors ['legal_person'] = '请填写法定代表人';
            }

            if (empty($post['registered_capital'])) {
                $errors ['registered_capital'] = '请填写注册资本';
            }

            if (empty($post['date_time'])) {
                $errors ['date_time'] = '请填写成立日期';
            }

            if (empty($post['operating_period'])) {
                $errors ['operating_period'] = '请填写营业期限';
            }

            if (empty($post['password']) or mb_strlen($post['real_password']) < 6) {
                $errors ['password'] = '请填写密码并且不能少于6位';
            }

            if (empty($post['c_password'])) {
                $errors ['c_password'] = '确认密码不能为空';
            }

            if ($post['password'] != $post['c_password']) {
                $errors ['c_password'] = '两次填写的密码不同';
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

            if (empty($post['company_id'])) {
                $errors ['other_error'] = '缺少企业标识';
            }

//            if (empty($post['email']) or !C::is_email($post['email'])) {
//                $errors ['email'] = '请填写正确的邮箱';
//            } else {
//                $user_info1 = M::Admin('User\\User', 'findUserByEmail', ['email'=>$post['email']]);
//                if (!empty($user_info1) and $user_info1['user_id'] != $post['user_id']) {
//                    $errors ['email'] = '此邮箱已被使用';
//                }
//            }

//            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
//                $errors ['tel'] = '请填写正确的11位手机号码';
//            } else {
//                $company_info2 = M::Vender('Company\\Company', 'findCompanyByTel', ['tel'=>$post['tel']]);
//                if (!empty($company_info2) and $company_info2['company_id'] != $post['company_id']) {
//                    $errors ['tel'] = '此手机号码已经被使用';
//                }
//            }

            $info = M::Vender('Company\\Company', 'findCompanyByCompanyId', ['company_id'=>$post['company_id']]);
            if (empty($info)) {
                $errors ['other_error'] = '没有找到信息';
            }

            $company_info = M::Vender('Company\\Company', 'findCompanyBySession');
            if ($company_info['pid'] != 0 and $company_info['company_id'] != $info['company_id']) { #非主账户无权修改其他账户的
                $errors ['other_error'] = '没有权限修改';
            } elseif($company_info['pid'] == 0 and $company_info['company_id'] != $info['pid'] and $company_info['company_id'] != $info['company_id']) { #主账户无权修改其他主账户或其他主账户下的子账户
                $errors ['other_error'] = '没有权限修改';
            }

            if (empty($post['name'])) {
                $errors ['name'] = '请填写名称';
            }

            if (empty($post['type'])) {
                $errors ['type'] = '请填写类型';
            }

            if (empty($post['address'])) {
                $errors ['address'] = '请填写住所';
            }

            if (empty($post['legal_person'])) {
                $errors ['legal_person'] = '请填写法定代表人';
            }

            if (empty($post['registered_capital'])) {
                $errors ['registered_capital'] = '请填写注册资本';
            }

            if (empty($post['date_time'])) {
                $errors ['date_time'] = '请填写成立日期';
            }

            if (empty($post['operating_period'])) {
                $errors ['operating_period'] = '请填写营业期限';
            }

            if (!empty($post['password'])) {
                if (mb_strlen($post['real_password']) < 6) {
                    $errors ['password'] = '密码不能少于6位';
                }

                if (empty($post['c_password'])) {
                    $errors ['c_password'] = '确认密码不能为空';
                }

                if ($post['password'] != $post['c_password']) {
                    $errors ['c_password'] = '两次填写的密码不同';
                }
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}