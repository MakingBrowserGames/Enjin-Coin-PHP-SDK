<?php
namespace EnjinCoin\Ethereum;
use Zend;
use EnjinCoin\Config;

class GethIpc implements IEthereumConnection
{
	private $socket = null;

	public function connect()
	{
		if (empty($this->socket)) {
			$ipc_path = Config::get()->ethereum->path;
			$this->socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
			socket_connect($this->socket, $ipc_path, 1);
		}

		return $this->socket;
	}

	public function disconnect()
	{
		socket_close($this->socket);
	}

	public function msg(string $method, array $params = [])
	{
		$buf = null;
		$msg = Zend\Json\Encoder::encode([
			'jsonrpc' => '2.0',
			'method' => $method,
			'params' => $params,
			'id' => mt_rand(1, 999999999)
		]);
		socket_send($this->socket, $msg, strlen($msg), MSG_EOF);
		socket_recv($this->socket, $buf, 8192, MSG_WAITALL);
		return $buf;
	}
}
