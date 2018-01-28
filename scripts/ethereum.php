<?php
require "../vendor/autoload.php";

use EnjinCoin\Config;
use EnjinCoin\Util\Constants;
use EnjinCoin\Ethereum as Eth;
use Zend\Json;

$subscriptions = [];

\Ratchet\Client\connect(Config::get()->ethereum->path)->then(function ($conn) {
	$conn->on('message', function ($msg) use ($conn) {
		//echo "Received: {$msg}\n";
		$response = Json\Decoder::decode($msg, Json\Json::TYPE_ARRAY);

		/*
		 * Assign the appropriate subscription IDs to their types
		 */
		if (!empty($response['error'])) {
			echo("\nError: " . var_export($response, true) . "\n");
		} else if (!empty($response['id'])) {
			echo("\nsubscribing to " . var_export($response, true) . "\n");
			switch ($response['id']) {
				case 1:
					Eth::subscribe($response['result'], 'logs');
					break;
				case 2:
					Eth::subscribe($response['result'], 'newPendingTransactions');
					break;
				case 3:
					Eth::subscribe($response['result'], 'newHeads');
					break;
				case 4:
					Eth::subscribe($response['result'], 'syncing');
					break;
				default:
					// do nothing
					break;
			}
		} /*
		 * Handle notifications
		 */
		else if (!empty($response['params']['subscription']) && isset(Eth::$subscriptions[$response['params']['subscription']])) {
			$method = Eth::$subscriptions[$response['params']['subscription']];
			if (method_exists('EnjinCoin\Ethereum', $method)) {
				Eth::$method($response['params']['result']);
			}
		}
	});

	/*
	 * Create subscriptions
	 */
	$conn->send('{"id": 1, "jsonrpc": "2.0", "method": "eth_subscribe", "params": ["logs", {"address": "' . Constants::tokenAddress . '"}]}');
	$conn->send('{"id": 2, "jsonrpc": "2.0", "method": "eth_subscribe", "params": ["newPendingTransactions"]}');
	$conn->send('{"id": 3, "jsonrpc": "2.0", "method": "eth_subscribe", "params": ["newHeads", {"includeTransactions": true}]}');
	//$conn->send('{"id": 4, "jsonrpc": "2.0", "method": "eth_subscribe", "params": ["syncing"]}');
}, function ($e) {
	echo "Could not connect: {$e->getMessage()}\n";
});
