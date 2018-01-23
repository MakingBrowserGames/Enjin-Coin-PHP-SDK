<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use EnjinCoin\Api\Apps;
use EnjinCoin\Api\Ethereum;
use EnjinCoin\Ethereum as Eth;
use EnjinCoin\Api\Identities;
use EnjinCoin\Auth;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
/**
 * @covers EnjinCoin\Api\Ethereum
 * See http://docs.mockery.io/en/latest/cookbook/mocking_hard_dependencies.html
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */	
final class EthereumTest extends MockeryTestCase {

	protected $ethereumApi;
	protected $ethereum_address;
	
	//Setup method called before every method 
	protected function setUp(): void {
		$this->ethereumApi = new Ethereum();
		$this->ethereum_address = '0x0000000000000000000000000000000' . rand(100000000, 999999999);
		
		Mockery::globalHelpers();
	}
	

	public function testGetBalances_EmptyArrayPassedIn(): void {
		$addresses = [];
		$result = $this->ethereumApi->getBalances($addresses);
		$this->assertEmpty($result);
	}
	
	public function testGetBalances_InvalidEthereumAddresses(): void {
		$addresses = ['1', '2', '3'];
		$result = $this->ethereumApi->getBalances($addresses);
		$this->assertEmpty($result);
	}
	
	public function testGetBalances_ValidEthereumAddress(): void {
		
		$mockEthereumService = Mockery::mock("overload:EnjinCoin\Ethereum");

		$mockEthereumService->shouldReceive('validateAddress')->once()->with($this->ethereum_address)->andReturn(true);
		$mockEthereumService->shouldReceive('msg')->once()->with(anyArgs())->andReturn(true);
		
		$addresses = [$this->ethereum_address];
		$result = $this->ethereumApi->getBalances($addresses);
		$this->assertNotEmpty($result);
	}
	
	public function tearDown()
    {
        Mockery::close();
    }
}
