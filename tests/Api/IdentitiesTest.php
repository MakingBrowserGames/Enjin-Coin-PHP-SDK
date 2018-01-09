<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Identities;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EnjinCoin\Api\Identities
 */	
final class IdentitiesTest extends TestCase {
	protected $identity_id = 0;
	protected $identity_code = '';
	protected $ethereum_address = '';
	protected $player_name = '';

	//Setup method called before every method 
	protected function setUp(): void {

		$this->ethereum_address = '0x0000000000000000000000000000000' . rand(100000000, 999999999);
		$this->player_name = 'testplayer' . rand(100000000, 999999999);

		$api = new Identities();
		$result = $api->create([
			'ethereum_address' => $this->ethereum_address,
			'player_name' => $this->player_name,
		]);
		$this->identity_id = $result['identity_id'];
		$this->identity_code = $result['identity_code'];
	}

	public function testCreate(): void {
		$api = new Identities();
		$result = $api->create([
			'player_name' => 'testcreate' . rand(100000000, 999999999)
		]);

		$this->assertArrayHasKey('identity_id', $result);
		$this->assertArrayHasKey('identity_code', $result);
	}
	
	//Pass in a random player_name key so that it doesnt already exist in the db
	public function testCreate_RandomKey(): void {
		$api = new Identities();
		$result = $api->create([
			'player_name'. rand(100000000, 999999999) => 'testcreate' . rand(100000000, 999999999)
		]);

		$this->assertArrayHasKey('identity_id', $result);
		$this->assertArrayHasKey('identity_code', $result);
	}
	public function testGet(): void {
		$api = new Identities();
		$result = $api->get(['identity_id' => $this->identity_id]);

		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name, $result[0]['player_name']);
	}

	public function testGet_LinkedIsTrue(): void {
		$api = new Identities();
		$result = $api->get(['identity_id' => $this->identity_id], true);

		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name, $result[0]['player_name']);
	}
	public function testGet_AfterIdentityIdIsSet(): void {
		$api = new Identities();
		$result = $api->get(['identity_id' => $this->identity_id], true, 1);

		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name, $result[0]['player_name']);
	}
	public function testGet_RandomField(): void {
		$api = new Identities();
		$result = $api->get(['random_field' => $this->identity_id]);

		$this->assertEmpty($result);
	}
	
	public function testUpdate(): void {
		$api = new Identities();
		$result = $api->update(['identity_id' => $this->identity_id], ['player_name' => $this->player_name . 'updated']);
		$this->assertEquals(true, $result);

		$result = $api->get(['identity_id' => $this->identity_id]);
		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name . 'updated', $result[0]['player_name']);
	}

	//Pass in the already set ethereum_address so that all branches of the code are hit
	public function testUpdate_EthereumAddressSetIsSameAsDB(): void {
		$api = new Identities();
		
		//Pass in the ethereum_address that was set in the setup method
		$result = $api->update(['identity_id' => $this->identity_id], ['player_name' => $this->player_name . 'updated', 'ethereum_address' => $this->ethereum_address]);
		$this->assertEquals(true, $result);

		$result = $api->get(['identity_id' => $this->identity_id]);
		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name . 'updated', $result[0]['player_name']);
	}
	
	public function testLink(): void {
		$new_eth_address = '0x1234567890123456789000' . rand(100000000, 999999999) . rand(100000000, 999999999);

		$api = new Identities();
		$result = $api->link($this->identity_code, $new_eth_address);
		$this->assertEquals(true, $result);

		$result = $api->get(['identity_id' => $this->identity_id]);
		$this->assertArrayHasKey('ethereum_address', $result[0]);
		$this->assertEquals($new_eth_address, $result[0]['ethereum_address']);
	}

	public function testDelete(): void {
		$api = new Identities();
		$result = $api->delete(['identity_id' => $this->identity_id]);
		$this->assertEquals(true, $result);

		$result = $api->get(['identity_id' => $this->identity_id], false);
		$this->assertEmpty($result);
	}

	public function tearDown(): void {
		$api = new Identities();
		$api->delete(['identity_id' => $this->identity_id]);
		$api->delete(['identity_id' => $this->identity_id + 1]);
	}
}
