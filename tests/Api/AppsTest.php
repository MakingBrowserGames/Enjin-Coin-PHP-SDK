<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Apps;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EnjinCoin\Api\Apps
 */
final class AppsTest extends BaseTest {
	protected $app_id = 0;
	protected $name = '';
	protected $app_auth_key = '';
	protected $appsApi;

	protected function setUp(): void {
		parent::setUp();
		$this->name = 'TestApp_' . rand(1, 999999999);

		$this->appsApi = new Apps();
		$result = $this->appsApi->create($this->name);
		$this->app_id = $result['app_id'];
		$this->app_auth_key = $result['app_auth_key'];
	}

	public function testGet(): void {
		$result = $this->appsApi->get($this->app_id);

		$this->assertArrayHasKey('app_id', $result);
		$this->assertArrayHasKey('name', $result);
		$this->assertEquals($this->app_id, $result['app_id']);
		$this->assertEquals($this->name, $result['name']);
		$this->assertNotEmpty($this->app_auth_key);
	}

	public function testGetByKey(): void {
		$result = $this->appsApi->getByKey($this->app_auth_key);

		$this->assertArrayHasKey('name', $result);
		$this->assertEquals($this->name, $result['name']);
	}

	/**
	 * @expectedException Exception
	 */
	public function testCreate_NameIsEmpty(): void {
		$this->name = '';
		$result = $this->appsApi->create($this->name);

		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Name must not be empty');
	}

	public function testCreate(): void {
		$this->name = 'TestApp_' . rand(1, 999999999);
		$result = $this->appsApi->create($this->name);
		$this->assertNotEmpty($result);
		$this->assertArrayHasKey('app_id', $result);
		$this->assertArrayHasKey('name', $result);
		$this->assertArrayHasKey('app_auth_key', $result);

		$this->app_id = $result['app_id'];

		$result = $this->appsApi->get($this->app_id);
		$this->assertArrayHasKey('name', $result);
		$this->assertEquals($this->name, $result['name']);
	}

	/**
	 * @expectedException Exception
	 */
	public function testUpdate_NameIsEmpty(): void {
		$this->name = '';
		$result = $this->appsApi->update($this->app_id, $this->name);

		$this->assertEquals(false, $result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Name must not be empty');
	}

	public function testUpdate(): void {
		$result = $this->appsApi->update($this->app_id, $this->name . 'updated');
		$this->assertEquals(true, $result);

		$result = $this->appsApi->get($this->app_id);
		$this->assertArrayHasKey('name', $result);
		$this->assertEquals($this->name . 'updated', $result['name']);
	}

	public function testDelete(): void {
		$result = $this->appsApi->delete($this->app_id);
		$this->assertEquals(true, $result);

		$result = $this->appsApi->get($this->app_id);
		$this->assertEmpty($result);
	}

	public function tearDown(): void {
		$this->appsApi->delete($this->app_id);
		$this->appsApi->delete($this->app_id + 1);
	}
}
