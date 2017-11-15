<?php
declare(strict_types=1);

namespace EnjinCoin\Test;
use EnjinCoin\Api\Apps;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;

/**
 * @covers Api
 */
final class AppsTest extends TestCase
{
	protected $app_id = 0;
	protected $name = '';

	protected function setUp(): void {
		$this->name = 'TestApp_' . rand(1,999999999);

		$api = new Apps();
		$result = $api->create($this->name);
		$this->app_id = $result['app_id'];
	}

	public function testCreate(): void
	{
		$api = new Apps();
		$result = $api->create($this->name . 'create-test');

		$this->assertArrayHasKey('app_id', $result);
		$this->assertArrayHasKey('name', $result);
		$this->assertEquals($this->name . 'create-test', $result['name']);
	}

	public function testGet(): void
	{
		$api = new Apps();
		$result = $api->get($this->app_id);

		$this->assertArrayHasKey('name', $result[0]);
		$this->assertEquals($this->name, $result[0]['name']);
	}

	public function testUpdate(): void
	{
		$api = new Apps();
		$result = $api->update($this->app_id, $this->name . 'updated');
		$this->assertEquals($result, true);

		$result = $api->get($this->app_id);
		$this->assertArrayHasKey('name', $result[0]);
		$this->assertEquals($this->name . 'updated', $result[0]['name']);
	}

	public function testDelete(): void
	{
		$api = new Apps();
		$result = $api->delete($this->app_id);
		$this->assertEquals($result, true);

		$result = $api->get($this->app_id);
		$this->assertEmpty($result);
	}

	public function tearDown(): void
	{
		$api = new Apps();
		$api->delete($this->app_id);
		$api->delete($this->app_id + 1);
	}
}
