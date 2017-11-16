<?php
$admin_id = (int)$_SESSION['admin_id'];
if (empty($admin_id)) {
    $_SESSION['login_error'] = '请登录';
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        exit(header('location:login.php'));
    } else {
        exit(json_decode(['status'=>-1, 'result'=>'请登录', `login_error`=>-1], JSON_UNESCAPED_UNICODE));
    }
}

