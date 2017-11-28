<?php
namespace EnjinCoin\Api;

use EnjinCoin\Config;
use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;

class Prices extends ApiBase {
	/**
	 * Get latest prices by exchange
	 *
	 * @param array $symbols
	 * @return array
	 */
	public function getPrices(array $symbols) {
		$allowed_symbols = Config::get()->prices->currencies;
		foreach($symbols as $i => $symbol) {
			if (!array_key_exists($symbol, $allowed_symbols)) {
				unset($symbols[$i]);
			}
		}

		$select = $this->db->select()
			->from('prices', 'value')
			->order('timestamp DESC')
			->limit(1);
		$row = DB::query($select)->toArray();
		$row = !empty($row[0]) ? $row[0] : [];

		$return_prices = array('timestamp' => $row['timestamp'], 'prices' => array());
		if (!empty($row) && !empty($symbols)) {
			$value = json_decode($row['value'], true);
			foreach($value as $symbol => $rates) {
				if (in_array($symbol, $symbols)) {
					$return_prices['prices'][$symbol] = $rates;
				}
			}
		}

		return $return_prices;
	}

	/**
	 * Get price history by symbol
	 *
	 * @param string $symbol
	 * @param string $interval
	 * @param string $start
	 * @param string $end
	 * @return array
	 */
	public function getHistory(string $symbol, string $interval, string $start, string $end) {

	}
}
