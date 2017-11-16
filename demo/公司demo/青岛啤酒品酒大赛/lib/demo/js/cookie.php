<?php
// +----------------------------------------------------------------------
// | Hero v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.anlulu.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: couth <coolboy@outlook.com> <http://www.anlulu.com>
// +----------------------------------------------------------------------

setcookie('b','123',time()+7200,'/');

if(isset($_COOKIE['a']))
var_dump($_COOKIE['a']);

if(isset($_COOKIE['b']))
var_dump($_COOKIE['b']);
/* End of file */