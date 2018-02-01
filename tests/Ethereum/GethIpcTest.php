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
		Config::get()->ethereum->path = '//./pipe/geth.ipc';
		$this->ipc = new GethIpc(Config::get()->ethereum->path);
	}

	public function testConnect() {
		$conn = $this->ipc->connect();
		self::assertNotEmpty($conn);
		self::assertNotFalse($conn);
	}

	public function testConnect_ExistingConnection() {
		$this->ipc->connect();
		self::assertFalse($this->ipc->connect());
	}

	public function testDisconnect() {
		$this->ipc->connect();
		self::assertTrue($this->ipc->disconnect());
	}

	public function testMsg() {
		$result = $this->ipc->msg('eth_protocolVersion', []);
		self::assertTrue(array_key_exists('result', $result));
	}

	protected function tearDown() {
		Config::get()->ethereum->path = 'ws://localhost:8546';
	}

}