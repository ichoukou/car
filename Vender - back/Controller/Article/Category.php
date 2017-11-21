<?php
namespace Admin\Controller\Article;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Category extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'article']);
        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['categorys'] = M::Admin('Article\\Category', 'findCategorys', ['params'=>$param]);
        $count = M::Admin('Article\\Category', 'findCategorysCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/Article/Category&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/Article/Category";

        $this->create_page();

        L::output(L::view('Article\\CategoryIndex', 'Admin', $this->data));
    }

    public function add_category()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'article']);

        $this->create_page();

        L::output(L::view('Article\\CategoryAdd', 'Admin', $this->data));
    }

    public function do_add_category()
    {
        if ($post = $this->validate_default()) {
            $id = M::Admin('Article\\Category', 'addCategory', ['post'=>$post]);

            if ($id > 0) {
                setcookie('success_info', '新增信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                $errors ['other_error'] = '新增信息失败';

                exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_category()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['category_id']);

        $category_id = (int)$_GET['category_id'];
        if (empty($category_id))
            exit(header("location:{$this->data['entrance']}route=Admin/Article/Category{$this->data['url']}"));

        $category_info = M::Admin('Article\\Category', 'findCategoryById', ['category_id'=>$category_id]);
        if (empty($category_info))
            exit(header("location:{$this->data['entrance']}route=Admin/Article/Category{$this->data['url']}"));

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'article']);

        $this->data['category_info'] = $category_info;

        $this->create_page();

        L::output(L::view('Article\\CategoryEdit', 'Admin', $this->data));
    }

    public function do_edit_category()
    {
        if ($post = $this->validate_edit()) {
            M::Admin('Article\\Category', 'editCategory', ['post'=>$post]);

            setcookie('success_info', '修改信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function autocomplete_article_type()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = ['module'=>'article','key'=>'setting_article_type'];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $return = M::Admin('Article\\Category', 'autocompleteFindArticleTypeSettings', ['post'=>$param]);

        $result = '';
        if (!empty($return)) {
            foreach ($return as $key=>$tool) {
                $result[$tool['setting_id']] = $tool;
            }
        }

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function remove_one()
    {
        $this->is_login();

        $category_id = (int)$_POST['category_id'];

        if (empty($category_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少信息标识'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Article\\Category', 'removeCategory', ['category_id'=>$category_id]);

        if ($return) {
            setcookie('success_info', '删除信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function remove_some()
    {
        $this->is_login();

        $category_ids = $_POST['category_ids'];

        if (count($category_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少信息标识'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Article\\Category', 'removeCategorys', ['category_ids'=>$category_ids]);

        if ($return) {
            setcookie('success_info', '删除多个信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'删除多个信息成功'], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(['status'=>-1, 'result'=>'删除多个信息失败'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function validate_default()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['article_type'])) {
                $errors ['article_type'] = '请选择文章分类';
            }

            if (empty($post['status'])) {
                $errors ['status'] = '请选择是否启用';
            }

            if (empty($post['title'])) {
                $errors ['title'] = '请填写分类标题';
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

            if (empty($post['category_id'])) {
                $errors ['other_error'] = '缺少标识';
            }

            if (empty($post['article_type'])) {
                $errors ['article_type'] = '请选择文章分类';
            }

            if (empty($post['status'])) {
                $errors ['status'] = '请选择是否启用';
            }

            if (empty($post['title'])) {
                $errors ['title'] = '请填写分类标题';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}