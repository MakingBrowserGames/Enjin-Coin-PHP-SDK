<?php
require "../vendor/autoload.php";

use Amp\Loop;
use EnjinCoin\Util\Db;
use EnjinCoin\Config;
use EnjinCoin\Prices;

function onRepeat($watcherID) {
	$model_prices = new Prices;
	$db = Db::getInstance();

	$timestamp = floor(time() / 60) * 60;
	$value = [];

	$markets = Config::get()->prices->markets;
	foreach($markets as $market_title => $options) {
		$ticker = $model_prices->fetchTicker($options['exchange'], $market_title);
		$value[$market_title] = $ticker['last'];

		// pusher updates
		if ($options['push_notifications']) {

		}
	}

	try {
		// try inserting first
		$insert = $db->insert('prices');
		$insert->values(array(
			'timestamp' => $timestamp,
			'value' => json_encode($value),
		), $insert::VALUES_MERGE);
		Db::query($insert);
	} catch (Exception $e) {
		// update if row (minute) already exists
		$update = $db->update('prices');
		$update->where(['timestamp' => $timestamp]);
		$update->set(['value' => json_encode($value)]);
		Db::query($update);
	}
}

Loop::run(function () {
	Loop::repeat(15000, "onRepeat");
});
