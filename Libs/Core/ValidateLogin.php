<?php
/**
 * 总控制器类
 */
namespace Libs\Core;

use Libs\Core\Model as M;
use Libs\Core\Session\SessionDbModel;

class ValidateLogin
{
    public function is_login($config)
    {
        if (empty($_SESSION['uid'])) {
            return false;
        } else {
            $db = new SessionDbModel($config);
            return $db->validate_login($_SESSION['uid']);
        }
    }

    public function is_admin_login($config)
    {
        if (empty($_SESSION['admin_id'])) {
            return false;
        } else {
            $db = new SessionDbModel($config);
            return $db->validate_admin_login($_SESSION['admin_id']);
        }
    }
}