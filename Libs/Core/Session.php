<?php
namespace Libs\Core;

use Libs\Core\Session\NormalSession;
use Libs\Core\Session\DbSession;

class Session
{
	public static $session_handler;
	public static $session_id;
	public static $data = [];

	public static function connect($config)
	{
		if ($config['save_type'] == 1) {
			#self::$session_handler = new NormalSession($config);
		} else {
			self::$session_handler = new DbSession($config);
			session_set_save_handler(self::$session_handler);
		}

		if (!session_id()) {
			ini_set('session.use_only_cookies', 'Off');
			ini_set('session.use_cookies', 'On');
			ini_set('session.use_trans_sid', 'Off');
			ini_set('session.cookie_httponly', 'On');
		
			if (isset($_COOKIE[session_name()]) && !preg_match('/^[a-zA-Z0-9,\-]{22,52}$/', $_COOKIE[session_name()])) {
				exit('Error: Invalid session ID!');
			}
			
			session_set_cookie_params(0, '/');
			session_start();
		}

		#self::start();
	}
		
	public static function start($key = 'default', $value = '')
	{
		if ($value) {
			self::$session_id = $value;
		} elseif (isset($_COOKIE[$key])) {
			self::$session_id = $_COOKIE[$key];
		} else {
			self::$session_id = self::createId();
		}	

		if (!isset($_SESSION[self::$session_id])) {
			$_SESSION[self::$session_id] = [];
		}

		self::$data = &$_SESSION[self::$session_id];

		if ($key != 'PHPSESSID') {
			setcookie(
				$key,
				self::$session_id,
				ini_get('session.cookie_lifetime'),
				ini_get('session.cookie_path'),
				ini_get('session.cookie_domain'),
				ini_get('session.cookie_secure'),
				ini_get('session.cookie_httponly')
			);
		}

		return self::$session_id;
	}
	
	public static function createId()
	{
		if (version_compare(phpversion(), '5.5.4', '>') == true) {
			return self::$session_handler->create_sid();
		} elseif (function_exists('random_bytes')) {
        	return substr(bin2hex(random_bytes(26)), 0, 26);
		} elseif (function_exists('openssl_random_pseudo_bytes')) {
			return substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
		} else {
			return substr(bin2hex(mcrypt_create_iv(26, MCRYPT_DEV_URANDOM)), 0, 26);
		}
	}

	public static function getId()
	{
		return self::$session_id;
	}

	public static function _destroy($key = 'default')
	{
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
		
		setcookie(
			$key,
			'',
			time() - 42000,
			ini_get('session.cookie_path'),
			ini_get('session.cookie_domain')
		);
	}
}