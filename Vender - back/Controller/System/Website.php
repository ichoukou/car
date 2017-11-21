<?php
namespace Vender\Controller\System;

use Libs\Core\ControllerVender AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Website extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->data['settings'] = M::Vender('Common\\Common', 'getSettings', ['module'=>'website']);
        $page_config = M::Vender('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['websites'] = M::Vender('System\\Website', 'findWebsites', ['params'=>$param]);
        $count = M::Vender('System\\Website', 'findWebsitesCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Vender/System/Website&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Vender/System/Website";

        $this->create_page();

        L::output(L::view('System\\WebsiteIndex', 'Vender', $this->data));
    }

    public function add_website()
    {
        $this->is_login();

        $this->data['settings'] = M::Vender('Common\\Common', 'getSettings', ['module'=>'website']);

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->create_page();

        L::output(L::view('System\\WebsiteAdd', 'Vender', $this->data));
    }

    public function do_add_website()
    {
        if ($post = $this->validate_default()) {

            $website_id = M::Vender('System\\Website', 'addWebsite', ['post'=>$post]);

            if ($website_id > 0) {
                setcookie('success_info', '新增网站设置信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增网站设置信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                $errors ['other_error'] = '新增网站设置信息失败';

                exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_website()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['website_id']);

        $website_id = (int)$_GET['website_id'];
        if (empty($website_id))
            exit(header("location:{$this->data['entrance']}route=Vender/System/Website{$this->data['url']}"));

        $website_info = M::Vender('System\\Website', 'findWebsiteByWebsiteId', ['website_id'=>$website_id]);
        if (empty($website_info))
            exit(header("location:{$this->data['entrance']}route=Vender/System/Website{$this->data['url']}"));

        $this->data['settings'] = M::Vender('Common\\Common', 'getSettings', ['module'=>'website']);

        if (!empty($website_info)) {
            $website_logo = explode('.', $website_info['website_logo']);
            $website_info['website_logo_cache'] =  URL_IMAGE_CACHE . "{$website_logo[0]}-" . URL_IMAGE_CACHE_WITH . "x" . URL_IMAGE_CACHE_HEIGHT. ".{$website_logo[1]}";

            $website_icon = explode('.', $website_info['website_icon']);
            $website_info['website_icon_cache'] =  URL_IMAGE_CACHE . "{$website_icon[0]}-" . URL_IMAGE_CACHE_WITH . "x" . URL_IMAGE_CACHE_HEIGHT. ".{$website_icon[1]}";
        }

        $this->data['website_info'] = $website_info;

        $this->create_page();

        L::output(L::view('System\\WebsiteEdit', 'Vender', $this->data));
    }

    public function do_edit_website()
    {
        if ($post = $this->validate_edit()) {
            M::Vender('System\\Website', 'editWebsite', ['post'=>$post]);

            setcookie('success_info', '编辑网站设置信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'编辑网站设置信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_one()
    {
        $this->is_login();

        $website_id = (int)$_POST['website_id'];

        if (empty($website_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少网站设置标示'], JSON_UNESCAPED_UNICODE));

        $return = M::Vender('System\\Website', 'removeWebsite', ['website_id'=>$website_id]);

        if ($return) {
            setcookie('success_info', '删除网站设置信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除网站设置信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除网站设置信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $website_ids = $_POST['website_ids'];

        if (count($website_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少课程标示'], JSON_UNESCAPED_UNICODE));


        $return = M::Vender('System\\Website', 'removeWebsites', ['website_ids'=>$website_ids]);

        if ($return) {
            setcookie('success_info', '删除多个网站设置信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个网站设置信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个网站设置信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['title'])) {
                $errors ['title'] = '请填写网店名称';
            }

            if (empty($post['url'])) {
                $errors ['url'] = '请填写网站地址 URL';
            }

            if (empty($post['meta_title'])) {
                $errors ['meta_title'] = '请填写Meta 标题';
            }

            $status = (int)$post['status'];
            if (empty($status) or !is_numeric($status)) {
                $errors ['status'] = '请选择状态';
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

            if (empty($post['website_id'])) {
                $errors ['other_error'] = '缺少网站设置标识';
            }

            if (empty($post['title'])) {
                $errors ['title'] = '请填写网店名称';
            }

            if (empty($post['url'])) {
                $errors ['url'] = '请填写网站地址 URL';
            }

            if (empty($post['meta_title'])) {
                $errors ['meta_title'] = '请填写Meta 标题';
            }

            $status = (int)$post['status'];
            if (empty($status) or !is_numeric($status)) {
                $errors ['status'] = '请选择状态';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}