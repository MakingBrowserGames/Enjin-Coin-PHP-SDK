<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use PHPUnit\Framework\TestCase;
use EnjinCoin\Util\Db;

/**
 * @covers EnjinCoin\Util\Db
 */	
final class DbTest extends TestCase {
	
	public function testCorrectDbClass(): void {
		$this->assertInstanceOf(
			\Zend\Db\Sql\Sql::class,
			Db::getInstance()
		);
	}
}
