<?php
namespace EnjinCoin\Api;

use EnjinCoin\Auth;
use Zend;
use EnjinCoin\ApiBase;
use EnjinCoin\Config;
use EnjinCoin\Notifications;

class Platform extends ApiBase {
	public function auth(string $auth_key) {
		return [
			'notifications' => [
				'method' => Config::get()->notifications->method,
				'client_info' => Notifications::getClientInfo(),
				'channels' => [
					'server' => Notifications::getSdkServerChannel($auth_key),
					'client' => Notifications::getSdkClientChannel($auth_key),
					'wallet' => Notifications::getWalletChannel($auth_key),
				],
				'role' => Auth::role()
			]
		];
	}

	/**
	 * Get all role names defined in the config
	 * @return array
	 */
	public function getRoles() {
		$roles = array_keys(Config::get()->permissions);

		if(!in_array(Auth::ROLE_GUEST, $roles))
			$roles[] = Auth::ROLE_GUEST;

		return $roles;
	}

	/**
	 * Return the role for an auth_key
	 * @param string $auth_key
	 * @return string
	 */
	public function getRole(string $auth_key) {
		$identities = new Identities();
		$result = $identities->get(['auth_key' => $auth_key]);
		if(!empty($result)) $identity = reset($result);
		else return Auth::ROLE_GUEST;

		if(!empty($identity['role']) && in_array($identity['role'], $this->getRoles())) {
			return $identity['role'];
		}
		return Auth::ROLE_GUEST;
	}
}
