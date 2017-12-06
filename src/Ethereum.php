<?php
namespace EnjinCoin;

use EnjinCoin\Ethereum\GethIpc;
use EnjinCoin\Ethereum\GethWebsocket;
use PHPUnit\Runner\Exception;

class Ethereum {
	public function __construct() {
		$this->connection = new GethWebsocket();
		$connect = $this->connection->connect();
		if (!$connect) {
			//throw new Exception('Could not connect to Ethereum.');
		}
	}

	public function test() {
		$result = $this->connection->msg('eth_protocolVersion');
		die(var_export($result, true));
	}

	public function msg($method, $params = []) {
		$result = $this->connection->msg($method, $params);
		return $result;
	}

	public static function validateAddress(string $address) {
		return preg_match("/^(0x)?[0-9a-fA-F]{40}$/", $address) !== 0;
	}

	public static function validateValue(string $value) {
		return true; // @todo
		//return preg_match("/^(0x)?[0-9a-fA-F]{40}$/", $address) !== 0;
	}
}
