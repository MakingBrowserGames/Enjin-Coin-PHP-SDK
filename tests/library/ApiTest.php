<?php
declare(strict_types=1);
namespace EnjinCoin;
use Zend;
use PHPUnit\Framework\TestCase;

/**
 * @covers Api
 */
final class ApiTest extends TestCase
{
	public function testHasDbInstance(): void
	{
		$this->assertInstanceOf(
			Zend\Db\Sql\Sql::class,
			Db::getInstance()
		);
	}
}
