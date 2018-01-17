<?php
namespace EnjinCoin\Util;

/**
 * Class Numbers
 * Big number and Hexadecimal functions
 * @package EnjinCoin\Util
 */
class Numbers {

	/**
	 * Function to decode hex
	 * @param string $input
	 * @return bool|int|string
	 */
	public static function decodeHex(string $input) {
		if (substr($input, 0, 2) === '0x') {
			$input = substr($input, 2);
		}

		if (preg_match('/[a-f0-9]+/', $input)) {
			return self::_bchexdec($input);
		}

		return $input;
	}

	/**
	 * Function to encode hex
	 * @param string $input
	 * @return string
	 */
	public static function encodeHex(string $input) {
		if (substr($input, 0, 2) !== '0x') {
			$hexPrefix = '0x';
		} else {
			$hexPrefix = '';
		}

		return $hexPrefix . self::_bcdechex($input);
	}

	/**
	 * @param string $dec
	 * @return string
	 */
	private static function _bcdechex(string $dec) {
		$hex = '';
		do {
			$last = bcmod($dec, 16);
			$hex = dechex($last) . $hex;
			$dec = bcdiv(bcsub($dec, $last), 16);
		} while ($dec > 0);
		return $hex;
	}

	/**
	 * @param string $hex
	 * @return int|string
	 */
	private static function _bchexdec(string $hex) {
		$dec = 0;
		$len = strlen($hex);
		for ($i = 1; $i <= $len; $i++) {
			$dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
		}
		return $dec;
	}
}