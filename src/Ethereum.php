<?php
namespace EnjinCoin;

use EnjinCoin\Ethereum\GethIpc;
use EnjinCoin\Ethereum\GethWebsocket;
use PHPUnit\Runner\Exception;

class Ethereum {
	public static $subscriptions = [];

	public function __construct() {
		$this->connection = new GethWebsocket();
		$connect = $this->connection->connect();
	}

	public static function subscribe($id, $handler) {
		self::$subscriptions[$id] = $handler;
	}

	public function test() {
		return $this->msg('eth_protocolVersion');
	}

	public function msg($method, $params = [], $full_response = false) {
		$response = $this->connection->msg($method, $params);
		return $full_response ? $response : $response['result'];
	}

	public static function validateAddress(string $address) {
		return preg_match("/^(0x)?[0-9a-fA-F]{40}$/", $address) !== 0;
	}

	public static function validateValue(string $value) {
		return true; // @todo
	}

	public static function logs($params) {
		echo("\nnewHeads\n");
		echo(var_export($params, true));
	}

	public static function newHeads($params) {
		echo("\nnewHeads\n");
		echo(var_export($params, true));
	}

	public static function newPendingTransactions($params) {
		//echo("\nnewPendingTransactions\n");
		//echo(var_export($params, true));
	}
}
