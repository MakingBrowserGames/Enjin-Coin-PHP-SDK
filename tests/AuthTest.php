<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Apps;
use EnjinCoin\Api\Identities;
use EnjinCoin\Auth;
use EnjinCoin\Util\Db;
use PHPUnit\Framework\TestCase;

final class AuthTest extends BaseTest {

	private static $tables = ['apps', 'identities'];

	protected $defaultEthereumAddress = '0x0';
	protected $defaultIdentityAuthKey = 'identity-auth-key';
	protected $defaultAppAuthKey = 'app-auth-key';

	public function testInit_AuthKeyEmpty(): void {
		$result = Auth::init(null);
		$this->assertFalse($result);
	}

	public function testInit_IdentityNotFound(): void {
		$result = Auth::init($this->defaultIdentityAuthKey);
		$this->assertFalse($result);
	}

	public function testInit_WalletRole(): void {
		$apps = new Apps();
		$app = $apps->create('auth-test-app');
		Auth::init($app['app_auth_key']);

		$identities = new Identities();
		$identity = $identities->create([
			'ethereum_address' => $this->defaultEthereumAddress,
			'auth_key' => $this->defaultIdentityAuthKey
		]);

		$result = Auth::init($this->defaultIdentityAuthKey);
		$this->assertTrue($result);
		$this->assertEquals(Auth::ROLE_WALLET, Auth::role());
		$this->assertEquals($identity['identity_id'], Auth::identity()['identity_id']);
	}

	public function testInit_AppNotFound(): void {
		$result = Auth::init($this->defaultAppAuthKey);
		$this->assertFalse($result);
	}

	public function testInit_AppRole(): void {
		$apps = new Apps();
		$app = $apps->create('auth-test-app');
		$result = Auth::init($app['app_auth_key']);
		$this->assertTrue($result);
		$this->assertEquals(Auth::ROLE_APP, Auth::role());
	}

	protected function tearDown(): void {
		foreach (AuthTest::$tables as $table) {
			$delete = Db::getDatabase()->delete($table);
			$delete->where('1=1');
			Db::query($delete);
		}
	}

}