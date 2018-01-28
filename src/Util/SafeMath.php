<?php
namespace EnjinCoin\Util;

use PHPUnit\Runner\Exception;

/**
 * Class SafeMath
 * Casts strings for processing 256-bit integers in PHP
 * @package EnjinCoin\Util
 */
class SafeMath {

	/**
	 * Function to add 2 values
	 * @param string $valA
	 * @param string $valB
	 * @throws  Exception if adding values failed
	 * @return float
	 */
	public static function add(string $valA, string $valB) {
		$output = floor($valA + $valB);
		if (!($output >= $valA)) {
			throw new Exception('adding values failed');
		}
		return $output;
	}

	/**
	 * Function to subtract 2 values
	 * @param string $valA
	 * @param string $valB
	 * @throws  Exception if subtracting values failed
	 * @return float
	 */
	public static function sub(string $valA, string $valB) {
		if (!($valB <= $valA)) {
			throw new Exception('subtracting values failed');
		}
		return floor($valA - $valB);
	}

	/**
	 * Function to divide 2 values
	 * @param string $valA
	 * @param string $valB
	 * @return float
	 */
	public static function div(string $valA, string $valB) {
		$output = floor($valA / $valB);
		return $output;
	}

	/**
	 * Function to multiply 2 values
	 * @param string $valA
	 * @param string $valB
	 * @throws  Exception if multiplying values failed
	 * @return float
	 */
	public static function mul(string $valA, string $valB) {
		$output = floor($valA * $valB);
		if (!($valA === 0 || $output / $valA == $valB)) {
			throw new Exception('multiplying values failed');
		}
		return $output;
	}
}