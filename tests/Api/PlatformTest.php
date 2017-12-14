<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Identities;
use EnjinCoin\Auth;
use EnjinCoin\Api\Apps;
use EnjinCoin\Api\Platform;
use PHPUnit\Framework\TestCase;

/**
 * @covers Platform
 */
final class PlatformTest extends TestCase {
	protected $app_auth_key = '';

	protected function setUp(): void {
		$result = (new Apps())->create('TestApp_' . rand(1, 999999999));
		$this->app_auth_key = $result['app_auth_key'];
		Auth::init($this->app_auth_key);
	}

	public function testAuth(): void {
		$api = new Platform();
		$result = $api->auth();

		$this->assertArrayHasKey('notifications', $result);
		$this->assertArrayHasKey('method', $result['notifications']);
		$this->assertArrayHasKey('client_info', $result['notifications']);
		$this->assertArrayHasKey('channels', $result['notifications']);
		$this->assertArrayHasKey('role', $result['notifications']);
		$this->assertNotEmpty($result['notifications']);
	}

	public function testNullIdentity() {
		$identity = Auth::identity();
		$this->assertNull($identity);
	}

	public function testGetRoles() {
		$api = new Platform();
		$result = $api->getRoles();
		$this->assertNotEmpty($result);
	}

	public function testGetRole() {
		$api = new Platform();
		$result = $api->getRole(Auth::authKey());
		$this->assertEquals(Auth::ROLE_GUEST, $result);
		//$this->assertEquals(Auth::ROLE_SERVER, $result);
	}

	public function tearDown(): void {
		$api = new Apps();
		$api->delete(Auth::appId());
	}
}
