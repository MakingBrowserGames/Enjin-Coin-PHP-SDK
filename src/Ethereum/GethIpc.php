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
	private $fp = null;

	/**
	 * Function to connect
	 * @return mixed
	 */
	public function connect() {
		if (empty($this->fp)) {
			$ipcPath = Config::get()->ethereum->path;
			$json = '{"jsonrpc":"2.0", "method":"eth_protocolVersion", "params":{}, "id":"1234"}';

			$pipe = popen($ipcPath, 'rw');
			fwrite($pipe, $json);
			exit(fread($pipe, 2048));
			pclose($pipe);


			$odata = "";

			Loop::run(function () {
				$data = "Testing\n";

				list($serverSock, $clientSock) = Socket\pair();

				\fwrite($serverSock, $data);
				\fclose($serverSock);

				$client = new Socket\ClientSocket($clientSock);

				while (($chunk = yield $client->read()) !== null) {
					$this->assertSame($data, $chunk);
				}
			});
			exit("data: " . $odata);


			$ipcPath = Config::get()->ethereum->path;
			$this->fp = fopen($ipcPath, 'r+');


			fwrite($this->fp, $json);
			while (!feof($this->fp)) {
				print fread($this->fp, 256);
				exit(var_export($this->fp, true));
			}

			fclose($this->fp);

			exit("\nDONE\n");


			$test = file_get_contents($ipcPath, false, null, 0, 128);
			exit(var_export($test, true));


			$this->fp = \socket_create(AF_INET, SOCK_STREAM, 0);
			//die(var_export($this->fp, true));
			//$error = \socket_last_error();
			//die(var_export($error, true));
			$connected = \socket_connect($this->fp, $ipcPath, 1);
			exit(var_export($connected, true));
		}

		return $this->fp;
	}

	/**
	 * Function to disconnect
	 */
	public function disconnect() {
		\socket_close($this->fp);
	}

	/**
	 * Function to send a message
	 * @param string $method
	 * @param array $params
	 * @return null
	 */
	public function msg(string $method, array $params = []) {
		$buf = null;
		$msg = Zend\Json\Encoder::encode([
			'jsonrpc' => '2.0',
			'method' => $method,
			'params' => $params,
			'id' => mt_rand(1, 999999999)
		]);
		\socket_send($this->fp, $msg, strlen($msg), MSG_EOF);
		\socket_recv($this->fp, $buf, 8192, MSG_WAITALL);
		return $buf;
	}
}
