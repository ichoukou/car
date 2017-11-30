<?php
namespace Front\Controller\User;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Inspection extends Controller
{
    public function index()
    {
        $this->is_login();

        $param = C::make_filter($_GET);
        $this->data['url'] = C::create_url($param);

        $this->data['car_id'] = (int)$_GET['car_id'];

        $this->create_page();

        L::output(L::view('User\\InspectionIndex', 'Front', $this->data));
    }
}