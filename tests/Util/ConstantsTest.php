<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use PHPUnit\Framework\TestCase;
use EnjinCoin\Util\Constants;

/**
 * @covers \EnjinCoin\Util\Constants 
 */
final class ConstantsTest extends TestCase {
	
	protected $constantsApi;
	//Setup method called before every method 
	protected function setUp(): void {
		$this->constantsApi = new Constants();
	}
	
	public function testConstants(): void {
		$this->assertNotNull(Constants::tokenAddress);	
		$this->assertNotNull(Constants::customTokensAddress);	
	}
}
