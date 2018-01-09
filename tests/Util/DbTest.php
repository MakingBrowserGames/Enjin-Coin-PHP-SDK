<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use PHPUnit\Framework\TestCase;
use EnjinCoin\Util\Db;
use Zend\Db\Sql\Sql;
/**
 * @covers EnjinCoin\Util\Db
 */	
final class DbTest extends TestCase {
	
	public function testCorrectDbClass(): void {
		//First time around $adapter is null
		$this->assertInstanceOf(
			\Zend\Db\Sql\Sql::class,
			Db::getInstance()
		);
		
		//Second time around $adapter is set
		$this->assertInstanceOf(
			\Zend\Db\Sql\Sql::class,
			Db::getInstance()
		);
	}
}

