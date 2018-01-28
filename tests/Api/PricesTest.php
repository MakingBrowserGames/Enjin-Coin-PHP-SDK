<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Prices;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;

/**
 * @covers EnjinCoin\Api\Prices
 */
final class PricesTest extends TestCase {

	protected $pricesApi;

	protected function setUp(): void {
		$this->pricesApi = new Prices();
	}

	public function testGetTotalSupply_ENJ(): void {
		$symbol = 'ENJ';
		$result = $this->pricesApi->getTotalSupply($symbol);
		$this->assertNotNull($result);
	}

	public function testGetTotalSupply_BTC(): void {
		$symbol = 'BTC';
		$result = $this->pricesApi->getTotalSupply($symbol);
		$this->assertNotNull($result);
	}

	public function testGetTotalSupply_LTC(): void {
		$symbol = 'LTC';
		$result = $this->pricesApi->getTotalSupply($symbol);
		$this->assertNotNull($result);
	}

	/**
	 * @expectedException Exception
	 */
	public function testGetTotalSupply_Exception(): void {
		$symbol = 'EXCEPTION';
		$result = $this->pricesApi->getTotalSupply($symbol);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('No supply available');
	}

	public function testGetCirculatingSupply_ENJ(): void {
		$symbol = 'ENJ';
		$result = $this->pricesApi->getCirculatingSupply($symbol);
		$this->assertNotNull($result);
	}

	/**
	 * @expectedException Exception
	 */
	public function testGetCirculatingSupply_EXCEPTION(): void {
		$symbol = 'EXCEPTION';
		$result = $this->pricesApi->getCirculatingSupply($symbol);
		$this->assertNotNull($result);
	}
}

?>