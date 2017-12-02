<?php
namespace EnjinCoin;

use EnjinCoin\Ethereum\GethIpc;
use EnjinCoin\Ethereum\Websockets;

class Ethereum {
	public function test() {
		$connection = new Websockets();
		$connection->connect();

		/*
		$connection->connect();
		$result = $connection->msg('eth_protocolVersion');
		die(var_export($result, true));
		*/
	}

	public static function validateAddress(string $address) {
		return preg_match("/^(0x)?[0-9a-fA-F]{40}$/", $address) !== 0;
	}

	public static function validateValue(string $value) {
		return true; // @todo
		//return preg_match("/^(0x)?[0-9a-fA-F]{40}$/", $address) !== 0;
	}
}
