<?php
declare(strict_types=1);
namespace EnjinCoin;
use PHPUnit\Framework\TestCase;

/**
 * @covers Config
 */
final class ConfigTest extends TestCase
{
	public function testHasCorrectConfigValues(): void
	{
		$config = Config::get();

		$this->assertObjectHasAttribute('platform', $config);
		$this->assertObjectHasAttribute('name', $config->platform);

		$this->assertObjectHasAttribute('db', $config);
		$this->assertObjectHasAttribute('adapter', $config->db);
		$this->assertObjectHasAttribute('database', $config->db);
		$this->assertObjectHasAttribute('username', $config->db);
		$this->assertObjectHasAttribute('password', $config->db);

		$this->assertObjectHasAttribute('ethereum', $config);
		$this->assertObjectHasAttribute('mode', $config->ethereum);
		$this->assertObjectHasAttribute('path', $config->ethereum);
	}
}
