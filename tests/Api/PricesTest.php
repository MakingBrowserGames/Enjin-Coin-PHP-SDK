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

	public function testGetTotalSupply_ENJ(): void {
		$symbol = 'ENJ';
		$api = new Prices();
		$result = $api->getTotalSupply($symbol);

		$this->assertNotNull($result);
	}
	
	public function testGetTotalSupply_BTC(): void {
		$symbol = 'BTC';
		$api = new Prices();
		$result = $api->getTotalSupply($symbol);

		$this->assertNotNull($result);
	}
	
	public function testGetTotalSupply_LTC(): void {
		$symbol = 'LTC';
		$api = new Prices();
		$result = $api->getTotalSupply($symbol);

		$this->assertNotNull($result);
	}

	/**
     * @expectedException Exception
     */
	public function testGetTotalSupply_Exception(): void {
		$symbol = 'EXCEPTION';
		$api = new Prices();
		$result = $api->getTotalSupply($symbol);

		
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('No supply available');
	}

	public function testGetCirculatingSupply_ENJ(): void {
		$symbol = 'ENJ';
		$api = new Prices();
		$result = $api->getCirculatingSupply($symbol);

		$this->assertNotNull($result);
	}
	
	/**
     * @expectedException Exception
     */
	public function testGetCirculatingSupply_EXCEPTION(): void {
		$symbol = 'EXCEPTION';
		$api = new Prices();
		$result = $api->getCirculatingSupply($symbol);

		$this->assertNotNull($result);
	}	
}

?>