<?php
namespace EnjinCoin\Ethereum;

use EnjinCoin\Config;
use Zend;
use Amp\Loop;
use Amp\Socket;

/**
 * Class GethIpc
 * @package EnjinCoin\Ethereum
 */
class GethIpc implements IEthereumConnection {
	private $path;
	private $fp;

	public function __construct($path) {
		$this->path = $path;
		$this->fp = null;
	}

	/**
	 * Function to connect
	 * @return fp
	 */
	public function connect() {
		if (empty($this->fp)) {
			$ipcPath = $this->path;
			$json = '{"jsonrpc":"2.0", "method":"eth_protocolVersion", "params":{}, "id":"1234"}';

			$file = fopen($ipcPath, 'r+');
			if ($file) {
				$this->fp = $file;
				$write = fwrite($file, $json);
				flush();
				if ($write) {
					$data = null;
					while (!feof($this->fp) && "\n" !== ($char = fgetc($file)))
						$data .= $char;
					$json = json_decode($data);
					if (array_key_exists('result', $json)) {
						return $this->fp;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Function to disconnect
	 */
	public function disconnect() {
		$result = false;
		if (!empty($this->fp)) {
			$result = fclose($this->fp);
			$this->fp = null;
		}
		return $result;
	}

	/**
	 * Function to send a message
	 * @param string $method
	 * @param array $params
	 * @return null
	 */
	public function msg(string $method, array $params = []) {
		$result = false;
		if (empty($this->fp))
			$result = $this->connect();
		if ($result) {
			$msg = Zend\Json\Encoder::encode([
				'jsonrpc' => '2.0',
				'method' => $method,
				'params' => $params,
				'id' => mt_rand(1, 999999999)
			]);
			$result = fwrite($this->fp, $msg);
			if ($result) {
				$data = null;
				while (!feof($this->fp) && "\n" !== ($char = fgetc($this->fp)))
					$data .= $char;
				$result = json_decode($data);
			}
		}
		return $result;
	}
}
