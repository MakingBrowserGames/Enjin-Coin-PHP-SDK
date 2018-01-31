<?php


namespace EnjinCoin\Test;


use EnjinCoin\Api\Apps;
use EnjinCoin\Auth;
use EnjinCoin\Config;
use EnjinCoin\Notifications;
use PHPUnit\Runner\Exception;

class NotificationsTest extends BaseTest {

	protected function setUp(): void {
		parent::setUp();

		$app = (new Apps())->create('app');
		Auth::init($app['app_auth_key']);

		Config::get()->notifications->method = 'pusher';
	}

	public function testGetAdapter_NoAdapter(): void {
		$this->expectException(Exception::class);
		Config::get()->notifications->method = 'unknown';
		Notifications::getClientInfo();
	}

	public function testGetWalletChannel(): void {
		$channel = Notifications::getWalletChannel(Auth::authKey());
		self::assertNotNull($channel);
	}

	public function testGetSdkServerChannel(): void {
		$channel = Notifications::getSdkServerChannel(Auth::authKey());
		self::assertNotNull($channel);
	}

	public function testGetSdkClientChannel(): void {
		$channel = Notifications::getSdkClientChannel(Auth::authKey());
		self::assertNotNull($channel);
	}

	public function testGetClientInfo(): void {
		$info = Notifications::getClientInfo();
		self::assertNotNull($info);
	}

}