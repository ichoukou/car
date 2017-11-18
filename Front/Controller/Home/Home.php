<?php
namespace Front\Controller\Home;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;

class Home extends Controller
{
    public function index()
    {
        $this->is_login();

        #$result = M::Front('Index\\Index','get_users',[]);

        $this->data['order_page']  = L::view('Home\\Order', 'Front', $this->data);
        $this->data['sale_page'] = L::view('Home\\Sale', 'Front', $this->data);
        $this->data['user_page'] = L::view('Home\\User', 'Front', $this->data);
        $this->data['online_page'] = L::view('Home\\Online', 'Front', $this->data);
        $this->data['activity_page'] = L::view('Home\\Activity', 'Front', $this->data);
        $this->data['online_page'] = L::view('Home\\Online', 'Front', $this->data);
        $this->data['new_order_page'] = L::view('Home\\NewOrder', 'Front', $this->data);

        $this->create_page();

        L::output(L::view('Home\\Home', 'Front', $this->data));
    }
}