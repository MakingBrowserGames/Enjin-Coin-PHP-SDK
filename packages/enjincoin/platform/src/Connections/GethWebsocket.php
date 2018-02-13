<?php
namespace EnjinCoin\Connections;

use EnjinCoin\Ethereum\IEthereumConnection;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use WebSocket\BadOpcodeException;
use WebSocket\Client;

/**
 * Class GethWebsocket
 * @package EnjinCoin\Ethereum
 */
class GethWebsocket implements IEthereumConnection {
    /**
     * @var Client
     */
    protected $client = null;

    /**
     * Function to connect
     */
    public function connect() {
        if (!empty($this->client))
            $this->disconnect();
        $this->client = new Client(config('enjincoin.ethereum.path'), [
            'timeout' => 15
        ]);
    }

    /**
     * Function to ubscribe
     * @return mixed
     * @throws BadOpcodeException
     */
    public function subscribe() {
        if (empty($this->client))
            $this->connect();
        $this->client->send('{"id": 1, "method": "eth_subscribe", "params": ["newHeads", {"includeTransactions": true}]}');
        return $this->client->receive();
    }

    /**
     * Function to disconnect
     */
    public function disconnect() {
        if (!empty($this->client))
            $this->client->close();
        $this->client = null;
    }

    /**
     * Function to send a message
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws BadOpcodeException
     */
    public function msg(string $method, array $params = []) {
        if (empty($this->client))
            $this->connect();
        $msg = json_encode([
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => mt_rand(1, 999999999)
        ]);
        $this->client->send($msg);
        return json_decode($this->client->receive(), true);
    }
}
