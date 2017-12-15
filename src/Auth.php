<?php
namespace EnjinCoin;

use EnjinCoin\Api\Apps;
use EnjinCoin\Api\Identities;
use Zend;

class Auth {
	const ROLE_GUEST = 'guest';
	const ROLE_WALLET = 'wallet';
	const ROLE_SERVER = 'server';
	const ROLE_CLIENT = 'client';

	private static $auth_key = '';
	private static $role = self::ROLE_GUEST;
	private static $app_id = 0;
	private static $identity = null;

	public static function init($auth_key) {
		if (empty($auth_key)) return false;

		// @todo: Identities/Apps should store hashed auth_key for security, and then validate here
		// Main concerns are bcrypt hash time slowing down each API request, perhaps a temp token can be used
		//$auth_hash = password_hash($auth_key, PASSWORD_BCRYPT);

		self::$auth_key = $auth_key;

		if (substr($auth_key, 0, 1) == 'a') {
			$apps = new Apps();
			$app = $apps->getByKey($auth_key);
			if (empty($app['app_id'])) return false;
			self::$app_id = (int) $app['app_id'];
			self::$role = self::ROLE_SERVER;
		} else {
			$identities = new Identities();
			$identity = $identities->get(['auth_key' => $auth_key]);
			if (empty($identity)) return false;
			self::$identity = reset($identity);
			self::$role = self::ROLE_WALLET;
		}

		return true;
	}

	public static function appId() {
		return self::$app_id;
	}

	public static function authKey() {
		return self::$auth_key;
	}

	public static function role() {
		return self::$role;
	}

	public static function identity() {
		return self::$identity;
	}
}
