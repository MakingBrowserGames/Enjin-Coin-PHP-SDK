<?php
namespace EnjinCoin\Ethereum;

use EnjinCoin\Config;
use Zend;
use WebSocket\Client;
require __DIR__ . '/../vendor/autoload.php';

class GethWebsocket implements IEthereumConnection {
    protected $client = null;

	public function connect() {
        $this->client = new Client(Config::get()->ethereum->path, [
            'timeout' => 15
        ]);
	}

	public function subscribe() {
        $this->client->send('{"id": 1, "method": "eth_subscribe", "params": ["newHeads", {"includeTransactions": true}]}');
        return $this->client->receive();
    }

	public function disconnect() {
	}

	public function msg(string $method, array $params = []) {
        $msg = Zend\Json\Encoder::encode([
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => mt_rand(1, 999999999)
        ]);
        $this->client->send($msg);
        return $this->client->receive();
	}
}
