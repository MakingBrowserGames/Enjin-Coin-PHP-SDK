<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Apps;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EnjinCoin\Api\Apps
 */	
final class AppsTest extends TestCase {
	protected $app_id = 0;
	protected $name = '';
	protected $app_auth_key = '';

	protected function setUp(): void {
		$this->name = 'TestApp_' . rand(1, 999999999);

		$api = new Apps();
		$result = $api->create($this->name);
		$this->app_id = $result['app_id'];
		$this->app_auth_key = $result['app_auth_key'];
	}

	public function testGet(): void {
		$api = new Apps();
		$result = $api->get($this->app_id);

		$this->assertArrayHasKey('app_id', $result);
		$this->assertArrayHasKey('name', $result);
		$this->assertEquals($this->app_id, $result['app_id']);
		$this->assertEquals($this->name, $result['name']);
		$this->assertNotEmpty($this->app_auth_key);
	}

	public function testGetByKey(): void {
		$api = new Apps();
		$result = $api->getByKey($this->app_auth_key);

		$this->assertArrayHasKey('name', $result);
		$this->assertEquals($this->name, $result['name']);
	}

	public function testUpdate(): void {
		$api = new Apps();
		$result = $api->update($this->app_id, $this->name . 'updated');
		$this->assertEquals(true, $result);

		$result = $api->get($this->app_id);
		$this->assertArrayHasKey('name', $result);
		$this->assertEquals($this->name . 'updated', $result['name']);
	}

	public function testDelete(): void {
		$api = new Apps();
		$result = $api->delete($this->app_id);
		$this->assertEquals(true, $result);

		$result = $api->get($this->app_id);
		$this->assertEmpty($result);
	}

	public function tearDown(): void {
		$api = new Apps();
		$api->delete($this->app_id);
		$api->delete($this->app_id + 1);
	}
}
