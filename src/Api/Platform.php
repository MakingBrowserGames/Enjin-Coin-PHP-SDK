<?php
namespace EnjinCoin\Api;

use Zend;
use EnjinCoin\ApiBase;
use EnjinCoin\Config;
use EnjinCoin\Notifications;

class Platform extends ApiBase {
	public function auth(string $auth_key) {
		$identities = new Identities();
		return [
			'notifications' => [
				'method' => Config::get()->notifications->method,
				'client_info' => Notifications::getClientInfo(),
				'channels' => [
					'server' => Notifications::getSdkServerChannel($auth_key),
					'client' => Notifications::getSdkClientChannel($auth_key),
					'wallet' => Notifications::getWalletChannel($auth_key),
				],
				'role' => $identities->getRole($auth_key)
			]
		];
	}
}
