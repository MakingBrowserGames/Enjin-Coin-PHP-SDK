<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Identities;
use EnjinCoin\Auth;
use EnjinCoin\Api\Apps;
use EnjinCoin\Api\Platform;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EnjinCoin\Api\Platform
 */
final class PlatformTest extends TestCase {
	protected $app_auth_key = '';
	protected $identity_id;
	protected $identity_code;
	protected $appsApi;
	protected $platformApi;
	protected $identitiesApi;

	protected function setUp(): void {
		$this->appsApi = new Apps();
		$result = $this->appsApi->create('TestApp_' . rand(1, 999999999));
		$this->app_auth_key = $result['app_auth_key'];
		Auth::init($this->app_auth_key);

		$this->platformApi = new Platform();
		$this->identitiesApi = new Identities();
	}

	public function testAuth(): void {
		$result = $this->platformApi->auth();

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
		$result = $this->platformApi->getRoles();
		$this->assertNotEmpty($result);
	}

	public function testGetRole() {
		$result = $this->platformApi->getRole(Auth::authKey());
		$this->assertEquals(Auth::ROLE_GUEST, $result);
		//$this->assertEquals(Auth::ROLE_SERVER, $result);
	}

	//TBD - more work
	public function testGetRole_IdentitiesAuthKeyAlreadyAvailable() {
		//Create the initial identity so that we can use it to associate an auth key
		$this->ethereum_address = '0x0000000000000000000000000000000' . rand(100000000, 999999999);
		$this->player_name = 'testplayer' . rand(100000000, 999999999);
		$result = $this->identitiesApi->create([
			'ethereum_address' => $this->ethereum_address,
			'player_name' => $this->player_name,
		]);
		$this->identity_code = $result['identity_code'];
		$this->identity_id = $result['identity_id'];

		//Link the identity so that an auth key is set on the record
		$new_eth_address = '0x1234567890123456789000' . rand(100000000, 999999999) . rand(100000000, 999999999);
		$result = $this->identitiesApi->link($this->identity_code, $new_eth_address);
		$this->assertEquals(true, $result);

		$result = $this->identitiesApi->get(['identity_id' => $this->identity_id]);
		/*$this->assertArrayHasKey('auth_key', $result[0]);

		$this->app_auth_key = $result['auth_key'];

		$api = new Platform();
		$result = $this->platformApi->getRole($this->app_auth_key);
		$this->assertEquals(Auth::ROLE_GUEST, $result);*/
		//$this->assertEquals(Auth::ROLE_SERVER, $result);
	}

	public function tearDown(): void {
		$this->identitiesApi->delete(['identity_id' => $this->identity_id]);

		$this->appsApi->delete(Auth::appId());
	}
}
