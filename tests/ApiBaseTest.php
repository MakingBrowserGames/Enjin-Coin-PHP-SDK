<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use PHPUnit\Framework\TestCase;
use EnjinCoin\ApiBase;

final class ApiBaseTest extends BaseTest {
	public function testHasDbInstance(): void {
		$apibase = new ApiBase();

		$this->assertInstanceOf(
			\Zend\Db\Sql\Sql::class,
			$apibase->db
		);
	}
}
