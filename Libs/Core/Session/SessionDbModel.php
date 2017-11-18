<?php
namespace Libs\Core\Session;

use Libs\Core\DbFactory AS DbFactory;

class SessionDbModel extends DbFactory
{
    private $config;         #配置文件
    private $expiration_time;#过期时间

    public function __construct($config)
    {
        $this->config = $config;
        $this->expiration_time = $config['expiration_time'] ?: 1800;
    }

    public function session_create()
    {
        $session_id = $_COOKIE[$this->config['session_name']];

        $find_sql = " SELECT * FROM " . self::$dp . "session_info WHERE `session_id` = :session_id ";

        $find_return = self::$db->get_one($find_sql, ['session_id'=>$session_id]);

        if (!empty($find_return)) {
            return $session_id;
        } else {
            if (function_exists('random_bytes')) {
                return substr(bin2hex(random_bytes(26)), 0, 26);
            } elseif (function_exists('openssl_random_pseudo_bytes')) {
                return substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
            } else {
                return substr(bin2hex(mcrypt_create_iv(26, MCRYPT_DEV_URANDOM)), 0, 26);
            }
        }
    }

    public function session_read($session_id)
    {
        $find_sql = " SELECT * FROM " . self::$dp . "session_info WHERE `session_id` = :session_id ";

        $find_return = self::$db->get_one($find_sql, ['session_id'=>$session_id]);

        setcookie($this->config['session_name'], $session_id, time() + $this->expiration_time);

        if (!empty($find_return) and strtotime($find_return['expiration_time']) + $this->expiration_time > time()) {
            return $find_return['data'];
        } else {
            return '';
        }
    }

    public function session_write($session_id, $session_encode_data, $arr_data)
    {
        $find_sql = " SELECT * FROM " . self::$dp . "session_info WHERE `session_id` = :session_id ";

        $find_return = self::$db->get_one($find_sql, ['session_id'=>$session_id]);

        if ($this->config['user_type'] == 'admin') {
            $uid = $arr_data['admin_id'] ?: 0;
        } elseif($this->config['user_type'] == 'vender'){
            $uid = $arr_data['vender_id'] ?: 0;
        } else {
            $uid = $arr_data['uid'] ?: 0;
        }

        if (empty($find_return)) {
            $insert_sql = "INSERT INTO " . self::$dp . "session_info (`session_id`,`data`,`uid`,`user_type`) VALUES ";

            self::$db->insert(
                $insert_sql,
                [
                    $session_id,
                    $session_encode_data,
                    $uid,
                    $this->config['user_type']
                ]
            );
        } else {
            $update_sql = "UPDATE " . self::$dp . "session_info SET `expiration_time` = CURRENT_TIMESTAMP(), `data` = :data, `uid` = :uid, `user_type` = :user_type WHERE `session_id` = :session_id";

            self::$db->update(
                $update_sql,
                [
                    'session_id' => $session_id,
                    'data'       => $session_encode_data,
                    'uid'        => $uid,
                    'user_type'  => $this->config['user_type'],
                ]
            );
        }

        return true;
    }

    public function session_destroy($session_id)
    {
        $sql = "DELETE FROM " . self::$dp . "session_info WHERE `session_id` = :session_id";

        self::$db->delete($sql, ['session_id'=>$session_id]);

        return true;
    }

    public function session_clear($maxlifetime)
    {
        $sql = "DELETE FROM " . self::$dp . "session_info WHERE UNIX_TIMESTAMP(expiration_time) < UNIX_TIMESTAMP(NOW()) - {$this->expiration_time}";

        self::$db->delete($sql);

        return true;
    }

    public function validate_login($uid)
    {
        $get_user_sql = "SELECT user_id FROM " . self::$dp . "user WHERE `user_id` = :user_id";

        $user_info = self::$db->get_one($get_user_sql,['user_id'=>$uid]);

        if (!empty($user_info['user_id'])) {
            if ($this->config['save_type'] == 2) {
                $get_session_sql = " SELECT session_id FROM " . self::$dp . "session_info WHERE `session_id` = :session_id AND `uid` = :uid";

                $session_info = self::$db->get_one($get_session_sql, ['uid'=>$uid, 'session_id'=>session_id()]);

                if (!empty($session_info['session_id'])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function validate_user_login($user)
    {
        $get_user_sql = "SELECT user_id FROM " . self::$dp . "user WHERE `user_id` = :user_id";

        $user_info = self::$db->get_one($get_user_sql, ['user_id'=>$user]);

        if (!empty($user_info['user_id'])) {
            if ($this->config['save_type'] == 2) {
                $get_session_sql = " SELECT session_id,expiration_time FROM " . self::$dp . "session_info WHERE `session_id` = :session_id AND `uid` = :uid";

                $session_info = self::$db->get_one($get_session_sql, ['uid'=>$user_info, 'session_id'=>session_id()]);

                if (!empty($session_info['session_id'])) {
                    if (strtotime($session_info['expiration_time']) + $this->expiration_time > time()) {
                        return true;
                    } else {
//                        $del_sql = " DELETE FROM " . self::$dp . "session_info WHERE `session_id` = :session_id AND `uid` = :uid";
//                        self::$db->delete($del_sql, ['uid'=>$admin, 'session_id'=>session_id()]);
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function validate_company_login($company)
    {
        $get_company_sql = "SELECT company_id FROM " . self::$dp . "company WHERE `company_id` = :company_id";

        $company_info = self::$db->get_one($get_company_sql, ['company_id'=>$company]);

        if (!empty($company_info['company_id'])) {
            if ($this->config['save_type'] == 2) {
                $get_session_sql = " SELECT session_id,expiration_time FROM " . self::$dp . "session_info WHERE `session_id` = :session_id AND `uid` = :uid";

                $session_info = self::$db->get_one($get_session_sql, ['uid'=>$company_info, 'session_id'=>session_id()]);

                if (!empty($session_info['session_id'])) {
                    if (strtotime($session_info['expiration_time']) + $this->expiration_time > time()) {
                        return true;
                    } else {
//                        $del_sql = " DELETE FROM " . self::$dp . "session_info WHERE `session_id` = :session_id AND `uid` = :uid";
//                        self::$db->delete($del_sql, ['uid'=>$admin, 'session_id'=>session_id()]);
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function validate_admin_login($admin)
    {
        $get_admin_sql = "SELECT admin_id FROM " . self::$dp . "admin WHERE `admin_id` = :admin_id";

        $admin_info = self::$db->get_one($get_admin_sql, ['admin_id'=>$admin]);

        if (!empty($admin_info['admin_id'])) {
            if ($this->config['save_type'] == 2) {
                $get_session_sql = " SELECT session_id,expiration_time FROM " . self::$dp . "session_info WHERE `session_id` = :session_id AND `uid` = :uid";

                $session_info = self::$db->get_one($get_session_sql, ['uid'=>$admin, 'session_id'=>session_id()]);

                if (!empty($session_info['session_id'])) {
                    if (strtotime($session_info['expiration_time']) + $this->expiration_time > time()) {
                        return true;
                    } else {
//                        $del_sql = " DELETE FROM " . self::$dp . "session_info WHERE `session_id` = :session_id AND `uid` = :uid";
//                        self::$db->delete($del_sql, ['uid'=>$admin, 'session_id'=>session_id()]);
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
}