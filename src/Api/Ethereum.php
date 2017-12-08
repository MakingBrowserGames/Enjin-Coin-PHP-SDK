<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use EnjinCoin\Ethereum as Eth;
use PHPUnit\Runner\Exception;

class Ethereum extends ApiBase {
	public function getBalances(array $addresses, string $tag = 'latest') {
		$data = [];
		foreach($addresses as $addr) {
			// validate address
			if (!Eth::validateAddress($addr)) {
				continue;
			}

			$model = new Eth;
			$data[$addr] = $model->msg('eth_getBalance', array($addr, $tag));
		}

		return $data;
	}

	public function estimateGas(array $eth_call) {
		$model = new Eth;
		return $model->msg('eth_estimateGas', array($eth_call));
	}

	public function getTransactionCount(string $address) {
		// validate address
		if (!Eth::validateAddress($address)) {
			throw new Exception('Invalid address.');
		}

		$model = new Eth;
		return $model->msg('eth_getTransactionCount', array($address, "latest"));
	}

	public function getTransactionByHash(string $hash) {
		$model = new Eth;
		return $model->msg('eth_getTransactionByHash', array($hash));
	}

	public function sendTransaction(array $transaction) {
		// validate address
		if (!Eth::validateAddress($transaction['from']) || !Eth::validateAddress($transaction['to'])) {
			throw new Exception('Invalid address.');
		}

		$model = new Eth;
		return $model->msg('eth_sendTransaction', array($transaction));
	}
}