<?php
namespace EnjinCoin\Api;

use EnjinCoin\ApiBase;
use PHPUnit\Runner\Exception;

/**
 * Class Prices
 * @package EnjinCoin\Api
 */
class Prices extends ApiBase {
	/**
	 * Return the total supply of a currency
	 * @todo: currently simple limited constant values for ENJ and BTC
	 * @param $symbol
	 * @throws Exception if no supply is available
	 * @return int
	 */
	public function getTotalSupply($symbol) {
		$value = 0;
		switch ($symbol) {
			case 'ENJ':
				$value = 1000000000;
				break;
			case 'BTC':
				$value = 21000000;
				break;
			case 'LTC':
				$value = 84000000;
				break;
			default:
				throw new Exception('No supply available');
		}
		return $value;
	}

	/**
	 * Return the circulating supply of a currency
	 * @todo: temporary value for ENJ, update to use token values
	 * @param $symbol
	 * @throws Exception if no supply is available
	 * @return int
	 */
	public function getCirculatingSupply($symbol) {
		$value = 0;
		switch ($symbol) {
			case 'ENJ':
				$value = 756192535;
				break;
			default:
				throw new Exception('No supply available');
		}
		return $value;
	}
}
