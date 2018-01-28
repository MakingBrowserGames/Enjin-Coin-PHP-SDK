<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use PHPUnit\Framework\TestCase;
use EnjinCoin\Config;

/**
 * @covers EnjinCoin\Config
 */
final class ConfigTest extends TestCase {

	public function testGet_HasCorrectConfigValues(): void {
		//First time we load config - the config object is null
		$config = Config::get();
		//Second time around the config object is loaded
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
