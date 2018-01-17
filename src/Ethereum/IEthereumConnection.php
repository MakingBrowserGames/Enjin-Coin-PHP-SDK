<?php
namespace EnjinCoin\Ethereum;

/**
 * Interface IEthereumConnection
 * @package EnjinCoin\Ethereum
 */
interface IEthereumConnection {

	/**
	 * Function to connect
	 * @return mixed
	 */
	public function connect();

	/**
	 * Function to disconnect
	 * @return mixed
	 */
	public function disconnect();

	/**
	 * Function to send a message
	 * @param string $method
	 * @param array $params
	 * @return mixed
	 */
	public function msg(string $method, array $params);
}
