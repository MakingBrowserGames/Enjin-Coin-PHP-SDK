<?php


namespace EnjinCoin\Test\Ethereum;


use EnjinCoin\Ethereum\GethWebsocket;
use PHPUnit\Framework\TestCase;

class GethWebsocketTest extends TestCase {

	/**
	 * @var GethWebsocket
	 */
	private $websocket;

	protected function setUp() {
		$this->websocket = new GethWebsocket();
	}

	public function testConnect() {
		self::assertEmpty($this->websocket->connect());
	}

	public function testConnect_ExistingConnection() {
		$this->websocket->connect();
		self::assertEmpty($this->websocket->connect());
	}

	public function testSubscribe() {
		self::assertContains('result', $this->websocket->subscribe());
	}

	public function testDisconnect() {
		$this->websocket->connect();
		self::assertEmpty($this->websocket->disconnect());
	}

	public function testMsg() {
		$result = $this->websocket->msg('eth_protocolVersion', []);
		self::assertEquals('0x3f', $result['result']);
	}

}