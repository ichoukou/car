<?php
namespace Vender\Controller\Company;

use Libs\Core\ControllerVender AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Analyze extends Controller
{
    public function index()
    {
        $this->is_login();

        $this->create_page();

        L::output(L::view('Company\\AnalyzeIndex', 'Vender', $this->data));
    }
}