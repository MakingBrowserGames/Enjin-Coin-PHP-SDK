<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use PHPUnit\Runner\Exception;

class Prices extends ApiBase {
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
