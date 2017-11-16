<?php
namespace Front\Controller\Index;

use Libs\Core\Controller AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;

class Home extends Controller
{
    public function index()
    {
        #$result = M::Front('Index\\Index','get_users',[]);

        #var_dump($result);

        L::output(L::view('Index\\Home', 'Front', $this->data));
    }
}