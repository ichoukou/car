<?php
namespace Front\Controller\Chat;

use Libs\Core\Controller AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;

class Index extends Controller
{
    public function index()
    {
        $this->is_login();

        L::output(L::view('Chat\\Index','Front',$this->data));
    }
}