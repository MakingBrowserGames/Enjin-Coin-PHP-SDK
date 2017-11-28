<?php
namespace EnjinCoin;

class Prices {
	public function fetchTicker($exchange, $market) {
		require_once('../vendor/ccxt/ccxt/ccxt.php');
		$class_name = "\ccxt\\" . $exchange;
		exit($class_name);
		$model = new $class_name();
		return $model->fetch_ticker($market);
	}
}
