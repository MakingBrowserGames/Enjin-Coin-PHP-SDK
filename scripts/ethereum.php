<?php
require "../vendor/autoload.php";

use Amp\Loop;
use EnjinCoin\Util\Db;
use EnjinCoin\Config;
use EnjinCoin\Prices;
use WebSocket\Client;

$client = new Client(Config::get()->ethereum->path, ['timeout' => 15]);
$client->send('{"id": 1, "method": "eth_subscribe", "params": ["newHeads", {"includeTransactions": true}]}');

while(true) {
    try {
        echo $client->receive();
        echo("\n");
    } catch (Exception $e) {
        echo($e->getMessage());
        echo("\n");
    }
}

/*
Loop::run(function () {
    Loop::repeat(15000, "onRepeat");
    Loop::repeat(43200000, "prune");
});
*/
