<?php
namespace EnjinCoin\Util;

/**
 * Class Numbers
 * Big number and Hexadecimal functions
 * @package EnjinCoin\Util
 */
class Numbers {
	public static function decodeHex(string $input) {
		if (substr($input, 0, 2) == '0x')
			$input = substr($input, 2);

		if (preg_match('/[a-f0-9]+/', $input))
			return self::bchexdec($input);

		return $input;
	}

	public static function encodeHex(string $input) {
		if (substr($input, 0, 2) != '0x')
			$hex_prefix = '0x';
		else
			$hex_prefix = '';

		return $hex_prefix . self::bcdechex($input);
	}

	private static function bcdechex(string $dec) {
		$hex = '';
		do {
			$last = bcmod($dec, 16);
			$hex = dechex($last) . $hex;
			$dec = bcdiv(bcsub($dec, $last), 16);
		} while ($dec > 0);
		return $hex;
	}

	private static function bchexdec(string $hex) {
		$dec = 0;
		$len = strlen($hex);
		for ($i = 1; $i <= $len; $i++) {
			$dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
		}
		return $dec;
	}
}