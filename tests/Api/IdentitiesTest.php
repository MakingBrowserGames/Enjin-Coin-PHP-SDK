<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Apps;
use EnjinCoin\Api\Identities;
use EnjinCoin\Auth;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EnjinCoin\Api\Identities
 */
final class IdentitiesTest extends TestCase {
	protected $app_auth_key = '';
	protected $identity_id = 0;
	protected $identity_code = '';
	protected $ethereum_address = '';
	protected $player_name = '';
	protected $appsApi;
	protected $identitiesApi;

	//Setup method called before every method 
	protected function setUp(): void {
		$this->appsApi = new Apps();
		$result = $this->appsApi->create('TestApp_' . rand(1, 999999999));
		$this->app_auth_key = $result['app_auth_key'];
		Auth::init($this->app_auth_key);

		$this->ethereum_address = '0x0000000000000000000000000000000' . rand(100000000, 999999999);
		$this->player_name = 'testplayer' . rand(100000000, 999999999);

		$this->identitiesApi = new Identities();
		$result = $this->identitiesApi->create([
			'ethereum_address' => $this->ethereum_address,
			'player_name' => $this->player_name,
			'identity_code' => 'fdsf',
		]);
		$this->identity_id = $result['identity_id'];
		$this->identity_code = $result['identity_code'];
	}

	public function testCreate(): void {
		$result = $this->identitiesApi->create([
			'player_name' => 'testcreate' . rand(100000000, 999999999)
		]);

		$this->assertArrayHasKey('identity_id', $result);
		$this->assertArrayHasKey('identity_code', $result);
	}

	//Pass in a random player_name key so that it doesnt already exist in the db
	public function testCreate_RandomKey(): void {
		$result = $this->identitiesApi->create([
			'player_name' . rand(100000000, 999999999) => 'testcreate' . rand(100000000, 999999999)
		]);

		$this->assertArrayHasKey('identity_id', $result);
		$this->assertArrayHasKey('identity_code', $result);
	}

	public function testGet(): void {
		$result = $this->identitiesApi->get(['identity_id' => $this->identity_id]);

		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name, $result[0]['player_name']);
	}

	public function testGet_LinkedIsTrue(): void {
		$result = $this->identitiesApi->get(['ethereum_address' => $this->ethereum_address, 'identity_id' => $this->identity_id], true);

		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name, $result[0]['player_name']);
	}

	public function testGet_AfterIdentityIdIsSet(): void {
		$result = $this->identitiesApi->get(['identity_id' => $this->identity_id], true, 1);

		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name, $result[0]['player_name']);
	}

	public function testGet_RandomField(): void {
		$result = $this->identitiesApi->get(['random_field' => $this->identity_id]);

		$this->assertEmpty($result);
	}

	public function testUpdate(): void {
		$result = $this->identitiesApi->update(['identity_id' => $this->identity_id], ['player_name' => $this->player_name . 'updated']);
		$this->assertEquals(true, $result);

		$result = $this->identitiesApi->get(['identity_id' => $this->identity_id]);
		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name . 'updated', $result[0]['player_name']);
	}

	//Pass in the already set ethereum_address so that all branches of the code are hit
	public function testUpdate_EthereumAddressSetIsSameAsDB(): void {

		//Pass in the ethereum_address that was set in the setup method
		$result = $this->identitiesApi->update(['identity_id' => $this->identity_id], ['player_name' => $this->player_name . 'updated', 'ethereum_address' => $this->ethereum_address]);
		$this->assertEquals(true, $result);

		$result = $this->identitiesApi->get(['identity_id' => $this->identity_id]);
		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name . 'updated', $result[0]['player_name']);
	}

	public function testLink(): void {
		$new_eth_address = '0x1234567890123456789000' . rand(100000000, 999999999) . rand(100000000, 999999999);

		$result = $this->identitiesApi->link($this->identity_code, $new_eth_address);
		$this->assertEquals(true, $result);

		$result = $this->identitiesApi->get(['identity_id' => $this->identity_id]);
		$this->assertArrayHasKey('ethereum_address', $result[0]);
		$this->assertEquals($new_eth_address, $result[0]['ethereum_address']);
	}

	public function testDelete(): void {
		$result = $this->identitiesApi->delete(['identity_id' => $this->identity_id]);
		$this->assertEquals(true, $result);

		$result = $this->identitiesApi->get(['identity_id' => $this->identity_id], false);
		$this->assertEmpty($result);
	}

	public function tearDown(): void {
		$this->identitiesApi->delete(['identity_id' => $this->identity_id]);
		$this->identitiesApi->delete(['identity_id' => $this->identity_id + 1]);

		$this->appsApi->delete(Auth::appId());
	}
}
