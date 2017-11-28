<?php
namespace EnjinCoin;

use Zend;

class Auth {
	private static $auth_key = null;
	private static $app_id = 0;

	public static function init($auth_key) {
		self::$auth_key = $auth_key;

		// todo: authenticate and allow access to certain API functions
	}

	public static function appId() {
		return self::$app_id;
	}
}