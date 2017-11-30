<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Identities;
use PHPUnit\Framework\TestCase;

/**
 * @covers Api
 */
final class IdentitiesTest extends TestCase {
	protected $identity_id = 0;
	protected $linking_code = '';
	protected $ethereum_address = '';
	protected $player_name = '';

	protected function setUp(): void {
		$this->ethereum_address = '0x0000000000000000000000000000000' . rand(100000000, 999999999);
		$this->player_name = 'testplayer' . rand(100000000, 999999999);

		$api = new Identities();
		$result = $api->create([
			'ethereum_address' => $this->ethereum_address,
			'player_name' => $this->player_name,
		]);
		$this->identity_id = $result['identity_id'];
		$this->linking_code = $result['linking_code'];
	}

	public function testCreate(): void {
		$api = new Identities();
		$result = $api->create([
			'player_name' => 'testcreate' . rand(100000000, 999999999)
		]);

		$this->assertArrayHasKey('identity_id', $result);
		$this->assertArrayHasKey('linking_code', $result);
	}

	public function testGet(): void {
		$api = new Identities();
		$result = $api->get(['identity_id' => $this->identity_id]);

		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name, $result[0]['player_name']);
	}

	public function testUpdate(): void {
		$api = new Identities();
		$result = $api->update(['identity_id' => $this->identity_id], ['player_name' => $this->player_name . 'updated']);
		$this->assertEquals($result, true);

		$result = $api->get(['identity_id' => $this->identity_id]);
		$this->assertArrayHasKey('player_name', $result[0]);
		$this->assertEquals($this->player_name . 'updated', $result[0]['player_name']);
	}

	public function testLink(): void {
		$api = new Identities();
		$result = $api->link($this->linking_code, '0x1234567890');
		$this->assertEquals($result, true);

		$result = $api->get(['identity_id' => $this->identity_id]);
		$this->assertArrayHasKey('ethereum_address', $result[0]);
		$this->assertEquals('0x1234567890', $result[0]['ethereum_address']);
	}

	public function testDelete(): void {
		$api = new Identities();
		$result = $api->delete(['identity_id' => $this->identity_id]);
		$this->assertEquals($result, true);

		$result = $api->get(['identity_id' => $this->identity_id], false);
		$this->assertEmpty($result);
	}

	public function tearDown(): void {
		$api = new Identities();
		$api->delete(['identity_id' => $this->identity_id]);
		$api->delete(['identity_id' => $this->identity_id + 1]);
	}
}
