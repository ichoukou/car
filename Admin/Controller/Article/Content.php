<?php
namespace Admin\Controller\Article;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Content extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'article']);
        $page_config = M::Admin('Common\\Common', 'getConfigs', ['module'=>'page']);
        $this->data['article_categorys'] = M::Admin('Article\\Content', 'autocompleteFindCategory');

        $default_param = [
            'limit'     => (int)$page_config['paging_limit']['value'],
            'page'      => 1
        ];

        $param = C::make_filter($_GET, $default_param);
        $param['start'] = ($param['page'] - 1) * (int)$page_config['paging_limit']['value'];
        $this->data['url'] = C::create_url($param);

        $this->data['contents'] = M::Admin('Article\\Content', 'findContents', ['params'=>$param]);
        $count = M::Admin('Article\\Content', 'findContentsCount', ['params'=>$param]);

        $pagination         = new Pagination();
        $pagination->total  = $count['total'];
        $pagination->page   = $param['page'];
        $pagination->limit  = $param['limit'];
        $pagination->url    = $this->data['entrance'] . "route=Admin/Article/Content&page={page}{$this->data['url']}";
        $this->data['pagination']   = $pagination->render();
        $this->data['results']      = sprintf($page_config['paging_rules']['value'], ($count['total']) ? (($param['page'] - 1) * $param['limit']) + 1 : 0, ((($param['page'] - 1) * $param['limit']) > ($count['total'] - $param['limit'])) ? $count['total'] : ((($param['page'] - 1) * $param['limit']) + $param['limit']), $count['total'], ceil($count['total'] / $param['limit']));

        $this->data = array_merge($this->data , $param);

        $this->data['success_info'] = $_COOKIE['success_info'];
        setcookie('success_info', '', -1);

        $this->data['error_info'] = $_COOKIE['error_info'];
        setcookie('error_info', '', -1);

        $this->data['search_url'] = "{$this->data['entrance']}route=Admin/Article/Content";

        $this->create_page();

        L::output(L::view('Article\\ContentIndex', 'Admin', $this->data));
    }

    public function add_content()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'article']);

        $this->create_page();

        L::output(L::view('Article\\ContentAdd', 'Admin', $this->data));
    }

    public function do_add_content()
    {
        if ($post = $this->validate_default()) {
            $id = M::Admin('Article\\Content', 'addContent', ['post'=>$post]);

            if ($id > 0) {
                setcookie('success_info', '新增信息成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>'新增信息成功'], JSON_UNESCAPED_UNICODE));
            } else {
                $errors ['other_error'] = '新增信息失败';

                exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function edit_content()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param, ['content_id']);

        $content_id = (int)$_GET['content_id'];
        if (empty($content_id))
            exit(header("location:{$this->data['entrance']}route=Admin/Article/Content{$this->data['url']}"));

        $content_info = M::Admin('Article\\Content', 'findContentById', ['content_id'=>$content_id]);
        if (empty($content_info))
            exit(header("location:{$this->data['entrance']}route=Admin/Article/Content{$this->data['url']}"));

        $this->data['settings'] = M::Admin('Common\\Common', 'getSettings', ['module'=>'article']);

        if (!empty($content_info)) {
            $image_path = explode('.', $content_info['image_path']);
            $content_info['image_path_cache'] =  URL_IMAGE_CACHE . "{$image_path[0]}-" . URL_IMAGE_CACHE_WITH . "x" . URL_IMAGE_CACHE_HEIGHT. ".{$image_path[1]}";

            if (!empty($content_info['other_images'])) {
                foreach ($content_info['other_images'] as $k=>$image) {
                    $image_path = explode('.', $image['image_path']);
                    $content_info['other_images'][$k]['image_path_cache'] =  URL_IMAGE_CACHE . "{$image_path[0]}-" . URL_IMAGE_CACHE_WITH . "x" . URL_IMAGE_CACHE_HEIGHT. ".{$image_path[1]}";
                }
            }
        }

        $this->data['content_info'] = $content_info;

        $this->create_page();

        L::output(L::view('Article\\ContentEdit', 'Admin', $this->data));
    }

    public function do_edit_content()
    {
        if ($post = $this->validate_edit()) {
            M::Admin('Article\\Content', 'editContent', ['post'=>$post]);

            setcookie('success_info', '修改信息成功', time() + 60);

            exit(json_encode(['status'=>1, 'result'=>'修改信息成功'], JSON_UNESCAPED_UNICODE));
        }
    }

    public function autocomplete_category()
    {
        $this->is_login();

        $filter_param = htmlspecialchars($_GET['filter_param']);

        $param = [];
        if (!empty($filter_param))
            $param['filter_param'] = $filter_param;

        $result = M::Admin('Article\\Content', 'autocompleteFindCategory', ['post'=>$param]);

        exit(json_encode(['status'=>1, 'result'=>$result], JSON_UNESCAPED_UNICODE));
    }

    public function remove_one()
    {
        $this->is_login();

        $content_id = (int)$_POST['content_id'];

        if (empty($content_id))
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少信息标识'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Article\\Content', 'removeContent', ['content_id'=>$content_id]);

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

        $content_ids = $_POST['content_ids'];

        if (count($content_ids) <= 0)
            exit(json_encode(['status'=>-1, 'result'=>'删除失败,缺少信息标识'], JSON_UNESCAPED_UNICODE));

        $return = M::Admin('Article\\Content', 'removeContents', ['content_ids'=>$content_ids]);

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

            if (count($post['category_id']) < 1) {
                $errors ['status'] = '请选择栏目';
            }

            if (empty($post['status'])) {
                $errors ['status'] = '请选择是否启用';
            }

            if (empty($post['title'])) {
                $errors ['title'] = '请填写分类标题';
            }

            if (empty($post['image_path'])) {
                $errors ['image_path'] = '请上传封面图';
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

            $post['content_id'] = (int)$post['content_id'];
            if (empty($post['content_id'])) {
                $errors ['other_error'] = '缺少标识';
            }

            if (count($post['category_id']) < 1) {
                $errors ['status'] = '请选择栏目';
            }

            if (empty($post['status'])) {
                $errors ['status'] = '请选择是否启用';
            }

            if (empty($post['title'])) {
                $errors ['title'] = '请填写分类标题';
            }

            if (empty($post['image_path'])) {
                $errors ['image_path'] = '请上传封面图';
            }

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}