<?php


namespace EnjinCoin\Test;

use EnjinCoin\Ethereum;
use PHPUnit\Framework\TestCase;

//TODO: remove dependence on live geth instance.
class EthereumTest extends TestCase {

	/**
	 * @var Ethereum
	 */
	private $ethereum;

	protected function setUp(): void {
		$this->ethereum = new Ethereum();
	}

	public function testConstructor() {
		self::assertNotNull($this->ethereum->connection);
	}

	public function testSubscribe() {
		self::assertEmpty($this->ethereum->subscribe('1', 'logs'));
	}

	public function testTest() {
		self::assertEquals('0x3f', $this->ethereum->test());
	}

	public function testMsg() {
		$result = $this->ethereum->msg('eth_protocolVersion');
		self::assertEquals('0x3f', $result);
	}

	public function testLogs() {
		self::assertEmpty($this->ethereum->logs([]));
	}

	public function testNewHeads() {
		self::assertEmpty($this->ethereum->newHeads([]));
	}

	public function testNewPendingTransactions() {
		self::assertEmpty($this->ethereum->newPendingTransactions([]));
	}

}