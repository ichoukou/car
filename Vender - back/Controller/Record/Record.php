<?php
namespace Admin\Controller\Record;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Record extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'record']);
        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['records'] = M::Admin('Record\\Record', 'findRecords', ['params'=>$param]);
        $count = M::Admin('Record\\Record', 'findRecordsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/Record/Record&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/Record/Record";

        $this->create_page();

        L::output(L::view('Record\\RecordIndex', 'Admin', $this->data));
    }

    public function add_record()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'record']);

        $this->create_page();

        L::output(L::view('Record\\RecordAdd', 'Admin', $this->data));
    }

    public function do_add_record()
    {
        if ($post = $this->validate_default()) {
            $record_id = M::Admin('Record\\Record', 'addRecord', ['post'=>$post]);

            if ($record_id > 0) {
                setcookie('success_info', '新增收支记录成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增收支记录成功'], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'新增收支记录失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_record()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['record_id']);

        $record_id = (int)$_GET['record_id'];
        if (empty($record_id))
            exit(header("location:{$this->data['entrance']}route=Admin/Record/Record{$this->data['url']}"));

        $record_info = M::Admin('Record\\Record', 'findRecordByRecordId', ['record_id'=>$record_id]);
        if (empty($record_info))
            exit(header("location:{$this->data['entrance']}route=Admin/Record/Record{$this->data['url']}"));

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'record']);

        $this->data['record_info'] = $record_info;

        $this->create_page();

        L::output(L::view('Record\\RecordEdit', 'Admin', $this->data));
    }

    public function do_edit_record()
    {
        if ($post = $this->validate_edit()) {
            M::Admin('Record\\Record', 'editRecord', ['post'=>$post]);

            setcookie('success_info', '修改收支记录信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改收支记录信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $record_id = (int)$_POST['record_id'];

        if (empty($record_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少收支记录标识'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Record\\Record', 'removeRecord', ['record_id'=>$record_id]);

        if ($return) {
            setcookie('success_info', '删除收支记录信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除收支记录信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除收支记录信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $record_ids = $_POST['record_ids'];

        if (count($record_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少收支记录标识'], JSON_UNESCAPED_UNICODE));


        $return = M::Admin('Record\\Record', 'removeRecords', ['record_ids'=>$record_ids]);

        if ($return) {
            setcookie('success_info', '删除多个收支记录信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个收支记录信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个收支记录信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['setting_id'])) {
                $errors ['setting_id'] = '请选择记录类型';
            }

            $post['money'] = (float)$post['money'];
            if (!is_numeric($post['money']) and !is_float($post['money'])) {
                $errors ['money'] = '金额必须为纯数字或者浮点数';
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

            if (empty($post['record_id'])) {
                $errors ['other_error'] = '缺少收支记录标识';
            }

            if (empty($post['setting_id'])) {
                $errors ['setting_id'] = '请选择记录类型';
            }

            $post['money'] = (float)$post['money'];
            if (!is_numeric($post['money']) and !is_float($post['money'])) {
                $errors ['money'] = '金额必须为纯数字或者浮点数';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}