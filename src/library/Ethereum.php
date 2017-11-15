<?php
namespace EnjinCoin;

class Ethereum
{
	public function test() {
		$connection = new GethIpc();
		$result = $connection->msg('eth_protocolVersion');
		die(var_export($result, true));
	}
}
