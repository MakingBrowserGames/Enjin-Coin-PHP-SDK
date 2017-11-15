<?php
declare(strict_types=1);

namespace EnjinCoin\Test;
use PHPUnit\Framework\TestCase;
use EnjinCoin\ApiBase;

/**
 * @covers Api
 */
final class ApiBaseTest extends TestCase
{
	public function testHasDbInstance(): void
	{
		$apibase = new ApiBase();

		$this->assertInstanceOf(
			\Zend\Db\Sql\Sql::class,
			$apibase->db
		);

		/*
		$this->assertInstanceOf(
			\Zend\Db\Sql\Sql::class,
			\EnjinCoin\Util\Db::getInstance()
		);
		*/
	}
}
