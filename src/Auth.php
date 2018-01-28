<?php
namespace EnjinCoin;

use EnjinCoin\Api\Apps;
use EnjinCoin\Api\Identities;
use Zend;

/**
 * Class Auth
 * @package EnjinCoin
 */
class Auth {
	const ROLE_PLATFORM = 0;
	const ROLE_APP = 1;
	const ROLE_CLIENT = 2;
	const ROLE_WALLET = 3;
	const ROLE_GUEST = 4;

	private static $authKey = '';
	private static $role = self::ROLE_GUEST;
	private static $appId = 0;
	private static $identity = null;

	/**
	 * Function to perform the auth initialization
	 * @param $authKey
	 * @return bool
	 */
	public static function init($authKey) {
		if (empty($authKey)) {
			return false;
		}

		// @todo: Identities/Apps should store hashed auth_key for security, and then validate here
		// Main concerns are bcrypt hash time slowing down each API request, perhaps a temp token can be used
		//$auth_hash = password_hash($auth_key, PASSWORD_BCRYPT);

		self::$authKey = $authKey;

		if (substr($authKey, 0, 1) === 'a') {
			$apps = new Apps();
			$app = $apps->getByKey($authKey);
			if (empty($app['app_id'])) {
				return false;
			}
			self::$appId = (int) $app['app_id'];
			self::$role = self::ROLE_APP;
		} else {
			$identities = new Identities();
			$identity = $identities->get(['auth_key' => $authKey]);
			if (empty($identity)) {
				return false;
			}
			self::$identity = reset($identity);
			self::$role = self::ROLE_WALLET;
		}

		return true;
	}

	/**
	 * Function to get the appId
	 * @return int
	 */
	public static function appId() {
		return self::$appId;
	}

	/**
	 * Function to get the authKey
	 * @return string
	 */
	public static function authKey() {
		return self::$authKey;
	}

	/**
	 * Function to get the role
	 * @return int
	 */
	public static function role() {
		return self::$role;
	}

	/**
	 * Function to get the identity
	 * @return null
	 */
	public static function identity() {
		return self::$identity;
	}
}
