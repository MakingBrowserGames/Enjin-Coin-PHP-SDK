<?php
namespace EnjinCoin\Api;

use EnjinCoin\Config;
use EnjinCoin\ApiBase;
use EnjinCoin\Util\Db;
use EnjinCoin\Prices as ModelPrices;
use PHPUnit\Runner\Exception;

class Prices extends ApiBase {
	/**
	 * Get latest prices by exchange
	 *
	 * @param bool|array $markets
	 * @return array
	 */
	public function getPrices($markets = false) {
		if (is_array($markets)) {
			$allowed_markets = Config::get()->prices->markets;
			foreach ($markets as $i => $market) {
				if (!array_key_exists($market, $allowed_markets)) {
					unset($markets[$i]);
				}
			}
		}

		$model = new ModelPrices;
		$row = $model->getLastPrices();

		$return_prices = [
			'timestamp' => $row['timestamp'],
			'prices' => $row['value'],
		];

		if (!empty($row) && !empty($markets)) {
			foreach ($return_prices['prices'] as $market => $rates) {
				if (!in_array($market, $markets)) {
					unset($return_prices['prices'][$market]);
				}
			}
		}

		return $return_prices;
	}

	/**
	 * Get price history by market
	 *
	 * @param $market
	 * @param string $interval
	 * @param bool $start
	 * @param bool $end
	 * @return array
	 */
	public function getHistory($market, $interval = 'd', $start = false, $end = false) {
		$allowed_intervals = array('m', 'h', 'd', 'w', 'mo', 'y');
		if (!in_array($interval, $allowed_intervals)) {
			$interval = 'd';
		}

		if (!$start) {
			$start = strtotime("1 month ago");
		}

		if (!$end) {
			$end = time();
		}

		$select = $this->db->select()
			->from('prices')
			->where(['timestamp > ?' => $start])
			->where(['timestamp < ?' => $end])
			->order('timestamp ASC');
		$rows = DB::query($select)->toArray();

		$prices = array();
		foreach ($rows as $row) {
			$values = json_decode($row['value'], true);
			if ($interval == 'm') {
				$base = floor($row['timestamp'] / 60) * 60;
			} else if ($interval == 'h') {
				$base = floor($row['timestamp'] / (60 * 60)) * 60 * 60;
			} else if ($interval == 'd') {
				$base = floor($row['timestamp'] / (60 * 60 * 24)) * 60 * 60 * 24;
			} else if ($interval == 'w') {
				$base = floor($row['timestamp'] / (60 * 60 * 24 * 7)) * 60 * 60 * 24 * 7;
			} else if ($interval == 'mo') {
				$base = floor($row['timestamp'] / (60 * 60 * 24 * 30)) * 60 * 60 * 24 * 30;
			} else if ($interval == 'y') {
				$base = floor($row['timestamp'] / (60 * 60 * 24 * 365)) * 60 * 60 * 24 * 365;
			}

			if (isset($values[$market])) {
				$prices[$base] = $values[$market];
			}
		}

		return $prices;
	}

	/**
	 * Get markets by exchange
	 *
	 * @param $exchange
	 * @return array
	 */
	public function getMarkets($exchange) {
		$model = new ModelPrices(strtolower($exchange));
		$markets = $model->fetchMarkets();

		$symbols = array();
		foreach ($markets as $market) {
			$symbols[] = $market['symbol'];
		}

		return $symbols;
	}

	/**
	 * Get supported exchanges
	 *
	 * @return mixed
	 */
	public function getExchanges() {
		$model = new ModelPrices();
		return $model->getExchanges();
	}

	/**
	 * Return the total supply of a currency
	 * @todo: currently simple limited constant values for ENJ and BTC
	 * @param $symbol
	 * @return int
	 */
	public function getTotalSupply($symbol) {
		switch ($symbol) {
			case 'ENJ':
				return 1000000000;
			case 'BTC':
				return 21000000;
			case 'LTC':
				return 84000000;
			default:
				throw new Exception('No supply available');
		}
	}

	/**
	 * Return the circulating supply of a currency
	 * @todo: temporary value for ENJ, update to use token values
	 * @param $symbol
	 * @return int
	 */
	public function getCirculatingSupply($symbol) {
		switch ($symbol) {
			case 'ENJ':
				return 756192535;
			default:
				throw new Exception('No supply available');
		}
	}
}
