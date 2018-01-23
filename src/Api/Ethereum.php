<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use EnjinCoin\Ethereum as Eth;
use PHPUnit\Runner\Exception;

/**
 * Class Ethereum
 * @package EnjinCoin\Api
 */
class Ethereum extends ApiBase {

	/**
	 * Function to get balances
	 * @param array $addresses
	 * @param string $tag
	 * @return array
	 */
	public function getBalances(array $addresses, string $tag = 'latest') {
		$data = [];
		
		foreach ($addresses as $addr) {
			// validate address
			if (!Eth::validateAddress($addr)) {
				continue;
			}

			$model = new Eth;
			$data[$addr] = $model->msg('eth_getBalance', array($addr, $tag));
		}

		return $data;
	}

	/**
	 * Function to estimate gas
	 * @param array $ethCall
	 * @return mixed
	 */
	public function estimateGas(array $ethCall) {
		$model = new Eth;
		return $model->msg('eth_estimateGas', array($ethCall));
	}

	/**
	 * Function to get the transaction count
	 * @param string $address
	 * @throws Exception if address is not valid
	 * @return mixed
	 */
	public function getTransactionCount(string $address) {
		// validate address
		if (!Eth::validateAddress($address)) {
			throw new Exception('Invalid address.');
		}

		$model = new Eth;
		return $model->msg('eth_getTransactionCount', array($address, "latest"));
	}

	/**
	 * Function to get the transaction hash
	 * @param string $hash
	 * @return mixed
	 */
	public function getTransactionByHash(string $hash) {
		$model = new Eth;
		return $model->msg('eth_getTransactionByHash', array($hash));
	}

	/**
	 * Function to send a raw transaction
	 * @param string $data
	 * @return mixed
	 */
	public function sendRawTransaction(string $data) {
		$model = new Eth;
		return $model->msg('eth_sendRawTransaction', array($data));
		/*
		// validate address
		if (!Eth::validateAddress($transaction['from']) || !Eth::validateAddress($transaction['to'])) {
			throw new Exception('Invalid address.');
		}

		$model = new Eth;
		return $model->msg('eth_sendTransaction', array($transaction));
		*/
	}

	/**
	 * Function to verify a signature
	 * @param string $address
	 * @param string $hash
	 * @param string $message
	 */
	public function verifySig(string $address, string $hash, string $message) {

	}

	/**
	 * Function to run a test
	 * @return mixed
	 */
	public function test() {
		$eth = new \EnjinCoin\Ethereum;
		return $eth->test();
	}
}