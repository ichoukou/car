<?php
namespace Vender\Controller\Account;

use Libs\Core\ControllerVender AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\BaiduOcr\OcrInit as Ocr;

class Account extends Controller
{
    public function index() {

        exit(header("location:{$this->data['entrance']}route=Vender/Account/Account/login"));
    }

    public function login()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        L::output(L::view('Account\\Login', 'Vender' ,$this->data));
    }

    public function do_login()
    {
        $tel = htmlspecialchars($_GET['tel']);
        $password = htmlspecialchars($_GET['password']);

        if (empty($tel) or !C::is_tel($tel))
            exit(json_encode(['status'=>-1, 'result'=>'请填写正确的11位手机号码'], JSON_UNESCAPED_UNICODE));

        if (empty($password))
            exit(json_encode(['status'=>-1, 'result'=>'请填写密码'], JSON_UNESCAPED_UNICODE));

        $result = M::Vender('Account\\Account', 'login', ['tel'=>$tel, 'password'=>$password]);

        if (!empty($result)) {
            $_SESSION['company_id'] = $result['company_id'];
            $_SESSION['name'] = $result['name'];
            $_SESSION['tel'] = $result['tel'];
            $_SESSION['group'] = $result['group'];

            exit(json_encode(['status'=>1, 'result'=>'登陆成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'账号或密码错误'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function register()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        $this->data['register_vender_info'] = json_decode($_SESSION['register_vender_info'], TURE);

        L::output(L::view('Account\\RegisterStep1', 'Vender', $this->data));
    }

    public function register_step2()
    {
        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->create_page();

        $this->data['register_vender_info'] = json_decode($_SESSION['register_vender_info'], TURE);
        if (empty($this->data['register_vender_info']))
            exit(header("location:{$this->data['entrance']}route=Vender/Account/Account/register"));

        L::output(L::view('Account\\RegisterStep2', 'Vender', $this->data));
    }

    public function register_success()
    {
        $this->create_page();

        L::output(L::view('Account\\RegisterSuccess', 'Vender', $this->data));
    }

    public function do_add_register_step1()
    {
        if ($post = $this->validate_default()) {
            $_SESSION['register_vender_info'] = json_encode($post, JSON_UNESCAPED_UNICODE);
            exit(json_encode(['status'=>1, 'result'=>'next'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_add_register_step2()
    {
        if ($post = $this->validate_step2()) {
            $company_id = M::Vender('Account\\Account', 'register', ['post'=>$post]);

            if ($company_id > 0) {
                M::Vender('Account\\Account', 'delSms', ['tel'=>$post['tel']]);
                $_SESSION['register_vender_info'] = '';

                exit(json_encode(['status'=>1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'电话号码被使用'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * 手机验证码
     */
    public function get_code()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);

            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
                exit(json_encode(['status'=>-1, 'result'=>'请填写正确的11位手机号码'], JSON_UNESCAPED_UNICODE));
            } elseif (M::Vender('Company\\Company', 'findCompanyByTel', ['tel'=>$post['tel']]) != '') {
                exit(json_encode(['status'=>-1, 'result'=>'此手机号码已经被使用'], JSON_UNESCAPED_UNICODE));
            }

            $sms_info = M::Vender('Account\\Account', 'getSmsInfo', ['tel'=>$post['tel']]);
            if (!empty($sms_info) and $sms_info['send_time'] + 60 >= time()) {
                exit(json_encode(['status'=>-1, 'result'=>'1分钟内只能发送一次'], JSON_UNESCAPED_UNICODE));
            }

            $rand_number = mt_rand(100000, 999999);
            $content = "尊敬的企业用户，您的注册验证码是{$rand_number}，10分钟内有效。如非本人操作请忽略！【买着网】";

            $sms_id = M::Vender('Account\\Account', 'addSms', ['tel'=>$post['tel'],'rand_number'=>$rand_number]);

            $return = C::sendSMS($post['tel'], $rand_number, $content);

            if ($return) {
                M::Vender('Account\\Account', 'editSms', ['sms_id' => $sms_id, 'return_type' => 1]);
                exit(json_encode(['status'=>1, 'result'=>'短信发送成功请查收'], JSON_UNESCAPED_UNICODE));
            } else {
                M::Vender('Account\\Account', 'editSms', ['sms_id' => $sms_id, 'return_type' => 2]);
                exit(json_encode(['status'=>-1, 'result'=>'短信发送失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * 百度Ocr图片识别
     */
    public function get_ocr()
    {
        #https://cloud.baidu.com/doc/OCR/OCR-PHP-SDK.html#.E8.A1.8C.E9.A9.B6.E8.AF.81.E8.AF.86.E5.88.AB 接口地址
        #http://blog.csdn.net/hu1991die/article/details/40585581
        #http://blog.csdn.net/hu1991die/article/details/41084153
        #https://yq.aliyun.com/articles/33569
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['base64_file']))
                exit(json_encode(['status'=>-1, 'result'=>'请上传图片'], JSON_UNESCAPED_UNICODE));

            $base64_file = explode(',', $_POST['base64_file']);
            if (empty($base64_file[1]))
                exit(json_encode(['status'=>-1, 'result'=>'请上传图片'], JSON_UNESCAPED_UNICODE));

            $ext_arr = explode(';', $base64_file[0]);
            $ext = explode('/', $ext_arr[0]);
            $ext = !empty($ext[1]) ? $ext[1] : 'jpeg';
            $file = base64_decode($base64_file[1]);

            $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
            $ext = 'jpg';
            $file_name = date('YmdHis', time()).$orderSn.mt_rand(100000, 999999).'.'.$ext;
            $file_path = ROOT_PATH.'Image'.DS.'upload'.DS.'vender'.DS.'register'.DS;

            file_put_contents($file_path.$file_name, $file);

            $scan = getimagesize($file_path.$file_name);
            $size = $scan[0] > $scan[1] ? $scan[0] : $scan[1];

            if ($size > 1024) {
                $n = 1;
                while ($n > 0.1) {
                    $n = $n - 0.1;

                    if (round($size * $n) < 1000) {
                        break;
                    }
                }

                $ir = new IR($file_path.$file_name);
                $ir->percent = $n;
                $ir->openImage();
                $ir->thumpImage();
                #$ir->showImage();
                $ir->saveImage($file_path.$file_name);
            }

            $ocr = new Ocr('10438599', 'cf4wHlLAC1V8OdkBpwiNYTbC', 'ZR5ImAmHf11bV7tGdz9aDKNvCAHoe3GA');
            $result = $ocr->analyze(file_get_contents($file_path.$file_name), 'businessLicense');

            $words_result = [];
            if (!empty($result['words_result'])) {
                foreach ($result['words_result'] as $key=>$value) {
                    if ($key == '单位名称')
                        $words_result['name'] = $value['words'];
                    if ($key == '法人')
                        $words_result['legal_person'] = $value['words'];
                    if ($key == '地址')
                        $words_result['address'] = $value['words'];
                    if ($key == '有效期')
                        $words_result['operating_period'] = $value['words'];
                }
            }

            if (empty($words_result)) {
                exit(json_encode(['status'=>-1, 'result'=>'没有识别出数据，请手动填写或使用更清晰的图片，且大小不超过4MB'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>1, 'result'=>$words_result], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function get_ocr_back()
    {
        #http://blog.csdn.net/hu1991die/article/details/40585581
        #http://blog.csdn.net/hu1991die/article/details/41084153
        #https://yq.aliyun.com/articles/33569
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST['base64_file']))
                exit(json_encode(['status'=>-1, 'result'=>'请上传图片'], JSON_UNESCAPED_UNICODE));

            $base64_file = explode(',', $_POST['base64_file']);
            if (empty($base64_file[1]))
                exit(json_encode(['status'=>-1, 'result'=>'请上传图片'], JSON_UNESCAPED_UNICODE));

            $ext_arr = explode(';', $base64_file[0]);
            $ext = explode('/', $ext_arr[0]);
            $ext = !empty($ext[1]) ? $ext[1] : 'jpeg';
            $file = base64_decode($base64_file[1]);

            $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
            $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

            $file_name = date('YmdHis', time()).$orderSn.mt_rand(100000, 999999).'.'.$ext;
            $file_path = ROOT_PATH.'Image'.DS.'upload'.DS.'vender'.DS;

            file_put_contents($file_path.$file_name, $file);

            $ocr = new Ocr('10376062', 'aKPVvLlnx1uiPtGQ4oUd7RV3', 'jVscvETAswCo7KSoUiaiPHMS6Bz0PKFZ');
            $result = $ocr->analyze(HTTP_SERVER.'Image/upload/vender/'.$file_name, 'basicGeneral');
            #var_dump($result);
//            $result['words_result'] = [
//                ['words'=>'名'],
//                ['words'=>'称天津顺天时科技有限公司'],
//                ['words'=>'类'],
//                ['words'=>'型有限责任公司(自然人独资)'],
//                ['words'=>'住'],
//                ['words'=>'所天津市东丽区津塘公路-407号2号楼3楼A区20'],
//                ['words'=>'号'],
//                ['words'=>'法定代表人徐丽英'],
//                ['words'=>'注册资本贰佰万元人民币'],
//                ['words'=>'成立日期二0一五年九月一日'],
//                ['words'=>'营业期限2015年09月01日至2045年08月31日']
//            ];

            $words_result = [];
            if (!empty($result['words_result'])) {
                foreach ($result['words_result'] as $key=>$value) {
                    array_push($words_result, $value['words']);
                }
            }

            $works_value = [];
            foreach ($words_result as $i=>$v) {
                if ($v == '名')
                    $works_value['name'] = mb_substr($words_result[$i+1], 1, mb_strlen($words_result[$i+1]), 'utf-8');
                if ($v == '类')
                    $works_value['type'] = mb_substr($words_result[$i+1], 1, mb_strlen($words_result[$i+1]), 'utf-8');
                if ($v == '住')
                    $works_value['address'] = mb_substr($words_result[$i+1], 1, mb_strlen($words_result[$i+1]), 'utf-8');
                if (stripos($v, '法定代表人') !== false)
                    $works_value['legal_person'] = mb_substr($v, 5, mb_strlen($v), 'utf-8');
                if (stripos($v, '注册资本') !== false)
                    $works_value['registered_capital'] = mb_substr($v, 4, mb_strlen($v), 'utf-8');
                if (stripos($v, '成立日期') !== false)
                    $works_value['date_time'] = mb_substr($v, 4, mb_strlen($v), 'utf-8');
                if (stripos($v, '营业期限') !== false)
                    $works_value['operating_period'] = mb_substr($v, 4, mb_strlen($v), 'utf-8');
            }

            exit(json_encode(['status'=>1, 'result'=>$works_value], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['tel']) or !C::is_tel($post['tel'])) {
                $errors ['tel'] = '请填写正确的11位手机号码';
            } elseif (M::Vender('Company\\Company', 'findCompanyByTel', ['tel'=>$post['tel']]) != '') {
                $errors ['tel'] = '此手机号码已经被使用';
            }

            if (empty($post['code'])) {
                $errors ['code'] = '验证码错误';
            } else {
                $sms_info = M::Vender('Account\\Account', 'validateSms', ['tel'=>$post['tel'], 'code'=>$post['code']]);
                if (empty($sms_info)) {
                    $errors ['tel'] = '验证码错误';
                } elseif ($sms_info['send_time'] + 600 < time()) {
                    $errors ['tel'] = '验证码已过期，请获取新的验证码';
                }
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
            exit(json_encode(['status'=>-1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_step2()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            $register_vender_info = json_decode($_SESSION['register_vender_info'], TURE);
            if (empty($register_vender_info))
                $errors ['other_error'] = '注册超时';

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

            if (!C::check_date_format($post['date_time'])) {
                $errors ['date_time'] = '请选择成立日期';
            }

            if (empty($post['operating_period'])) {
                $errors ['operating_period'] = '请填写营业期限';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            $post['tel'] = $register_vender_info['tel'];
            $post['password'] = $register_vender_info['password'];

            return $post;
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'注册失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function do_logout()
    {
        if (session_id()) {
            session_destroy();
        }

        #exit(json_encode(['status'=>1, 'result'=>'退出成功'], JSON_UNESCAPED_UNICODE));
        exit(header("location:{$this->data['entrance']}route=Vender/Account/Account/login"));
    }
}