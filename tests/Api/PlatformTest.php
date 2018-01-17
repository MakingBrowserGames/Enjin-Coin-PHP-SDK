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

	//TBD - more work
	public function testGetRole_IdentitiesAuthKeyAlreadyAvailable() {
		//Create the initial identity so that we can use it to associate an auth key
		$this->ethereum_address = '0x0000000000000000000000000000000' . rand(100000000, 999999999);
		$this->player_name = 'testplayer' . rand(100000000, 999999999);
		$identitiesApi = new Identities();
		$result = $identitiesApi->create([
			'ethereum_address' => $this->ethereum_address,
			'player_name' => $this->player_name,
		]);
		$this->identity_code = $result['identity_code'];
		$this->identity_id = $result['identity_id'];

		//Link the identity so that an auth key is set on the record
		$new_eth_address = '0x1234567890123456789000' . rand(100000000, 999999999) . rand(100000000, 999999999);
		$result = $identitiesApi->link($this->identity_code, $new_eth_address);
		$this->assertEquals(true, $result);

		$result = $identitiesApi->get(['identity_id' => $this->identity_id]);
		/*$this->assertArrayHasKey('auth_key', $result[0]);

		$this->app_auth_key = $result['auth_key'];

		$api = new Platform();
		$result = $api->getRole($this->app_auth_key);
		$this->assertEquals(Auth::ROLE_GUEST, $result);*/
		//$this->assertEquals(Auth::ROLE_SERVER, $result);
	}
	
	public function tearDown(): void {
	    $api = new Identities();
        $api->delete(['identity_id' => $this->identity_id]);

		$api = new Apps();
		$api->delete(Auth::appId());
	}
}
