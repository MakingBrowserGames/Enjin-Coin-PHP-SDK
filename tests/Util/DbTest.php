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

	public function testGetInstance_AdapterIsEmpty(): void {
		Db::$adapter = '';
		$db = Db::getInstance();

		$this->assertInstanceOf(
			\Zend\Db\Sql\Sql::class,
			$db
		);
	}

	public function testGetInstance(): void {
		$db = Db::getInstance();

		$this->assertInstanceOf(
			\Zend\Db\Sql\Sql::class,
			$db
		);
	}

	public function testSelect(): void {

		$sql = new Sql(Db::$adapter);
		$select = $sql->select();
		$select->from('apps');

		$result = Db::query($select);

		$this->assertNotNull($result);
	}
}

