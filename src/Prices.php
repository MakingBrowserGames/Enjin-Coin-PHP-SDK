<?php
namespace EnjinCoin;

use PHPUnit\Runner\Exception;
use EnjinCoin\Util\Db;

class Prices {
	public function __construct($exchange = 'hitbtc') {
		require_once '../vendor/ccxt/ccxt/ccxt.php';

		// make sure this exchange is available within ccxt
		$class_name = "\ccxt\hitbtc";
		$test_model = new $class_name;
		$ccxt_exchanges = $test_model::$exchanges;
		if (!in_array($exchange, $ccxt_exchanges)) {
			throw new Exception('Invalid exchange');
		}

		// load the model
		$class_name = "\ccxt\\" . $exchange;
		$this->model = new $class_name();
	}

	/**
	 * Get support exchanges
	 *
	 * @return mixed
	 */
	public function getExchanges() {
		return $this->model::$exchanges;
	}

	/**
	 * Fetch ticker data by market (various price information)
	 *
	 * @param $market
	 * @return mixed
	 */
	public function fetchTicker($market) {
		return $this->model->fetch_ticker($market);
	}

	/**
	 * Fetch markets by exchange
	 *
	 * @return mixed
	 */
	public function fetchMarkets() {
		return $this->model->fetch_markets();
	}

	/**
	 * Get the latest prices
	 *
	 * @return array
	 */
	public function getLastPrices() {
		$db = Db::getInstance();
		$select = $db->select()
			->from('prices')
			->order('timestamp DESC')
			->limit(1);
		$rows = Db::query($select)->toArray();

		if (!empty($rows[0])) {
			$row = $rows[0];
			$row['value'] = json_decode($row['value'], true);
			return $row;
		}

		return array();
	}
}
