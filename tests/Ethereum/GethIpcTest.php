<?php


namespace EnjinCoin\Test\Ethereum;


use EnjinCoin\Config;
use EnjinCoin\Ethereum\GethIpc;
use PHPUnit\Framework\TestCase;

class GethIpcTest extends TestCase {

	/**
	 * @var GethIpc
	 */
	private $ipc;

	protected function setUp() {
		Config::get()->ethereum->path = '\\\\.\\pipe\\get.ipc';
		$this->ipc = new GethIpc();
	}

	public function testConnect() {
		self::assertEmpty($this->ipc->connect());
	}

	public function testConnect_ExistingConnection() {
		$this->ipc->connect();
		self::assertEmpty($this->ipc->connect());
	}

	public function testDisconnect() {
		$this->ipc->connect();
		self::assertEmpty($this->ipc->disconnect());
	}

	public function testMsg() {
		$result = $this->ipc->msg('eth_protocolVersion', []);
		self::assertEquals('0x3f', $result['result']);
	}

	protected function tearDown() {
		Config::get()->ethereum->path = 'ws://localhost:8546';
	}

}