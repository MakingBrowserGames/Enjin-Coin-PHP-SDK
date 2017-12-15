<?php
require "../vendor/autoload.php";

use Amp\Loop;
use EnjinCoin\Util\Db;
use EnjinCoin\Config;
use EnjinCoin\Prices;
use WebSocket\Client;

$client = new Client(Config::get()->ethereum->path, ['timeout' => 3]);

//$json = '{"jsonrpc":"2.0", "id": 1, "method": "eth_subscribe", "params": ["newHeads", {"includeTransactions": true}]}';
//$json = '{"id": 1, "method": "eth_subscribe", "params": ["logs", {"address": "0x1f573d6fb3f13d689ff844b4ce37794d79a7ff1c"}]}';
//$json = '{"id": 1, "method": "eth_newPendingTransactionFilter", "params": []}';
//$json = '{"jsonrpc":"2.0","method":"eth_newPendingTransactionFilter","params":[],"id":73}';
$json = '{"jsonrpc":"2.0","method":"eth_newBlockFilter","params":[],"id":73}';

$client->send($json);
$result = $client->receive();
echo($result);
$data = json_decode($result, true);
$id = $data['result'];

while (true) {
	try {
		$json = '{"jsonrpc":"2.0","method":"eth_getFilterChanges","params":["' . $id . '"],"id":73}';
		sleep(1);
		$client->send($json);
		echo $client->receive();
		echo("\n");
	} catch (Exception $e) {
		//echo($e->getMessage());
		echo("\n");
	}
}

/*
Loop::run(function () {
    Loop::repeat(15000, "onRepeat");
    Loop::repeat(43200000, "prune");
});
*/
