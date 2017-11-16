<?php
namespace Admin\Controller\Home;

use Libs\Core\ControllerAdmin AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;

class Home extends Controller
{
    public function index()
    {
        $this->is_login();

        #$result = M::Front('Index\\Index','get_users',[]);

        $this->data['order_page']  = L::view('Home\\Order', 'Admin', $this->data);
        $this->data['sale_page'] = L::view('Home\\Sale', 'Admin', $this->data);
        $this->data['user_page'] = L::view('Home\\User', 'Admin', $this->data);
        $this->data['online_page'] = L::view('Home\\Online', 'Admin', $this->data);
        $this->data['activity_page'] = L::view('Home\\Activity', 'Admin', $this->data);
        $this->data['online_page'] = L::view('Home\\Online', 'Admin', $this->data);
        $this->data['new_order_page'] = L::view('Home\\NewOrder', 'Admin', $this->data);

        $this->create_page();

        L::output(L::view('Home\\Home', 'Admin', $this->data));
    }
}