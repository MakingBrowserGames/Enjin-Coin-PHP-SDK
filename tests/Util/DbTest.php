<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use PHPUnit\Framework\TestCase;
use EnjinCoin\Util\Db;
use Zend\Db\Sql\Sql;

/**
 * @covers \EnjinCoin\Util\Db
 */
final class DbTest extends TestCase {

	public function testGetInstance(): void {
		$db = Db::getInstance();

		$this->assertInstanceOf(
			\EnjinCoin\Util\Db::class,
			$db);
	}

	public function testGetDatabase(): void {
		$db = Db::getDatabase();

		$this->assertInstanceOf(
			\Zend\Db\Sql\Sql::class,
			$db
		);
	}

	public function testGetAdapter(): void {
		$adapter = Db::getAdapter();

		$this->assertInstanceOf(
			\Zend\Db\Adapter\Adapter::class,
			$adapter
		);
	}

	public function testSelect(): void {
		$db = Db::getDatabase();
		$select = $db->select();
		$select->from('apps');

		$result = Db::query($select);

		$this->assertNotNull($result);
	}
}

