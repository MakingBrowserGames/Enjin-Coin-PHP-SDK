<?php
namespace EnjinCoin;
use \EnjinCoin\Ethereum\GethIpc;

class Ethereum
{
	public function test() {
		$connection = new GethIpc();
		$connection->connect();
		$result = $connection->msg('eth_protocolVersion');
		die(var_export($result, true));
	}
}
