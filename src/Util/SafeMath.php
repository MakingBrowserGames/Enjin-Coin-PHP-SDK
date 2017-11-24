<?php
namespace EnjinCoin\Util;

/**
 * Class SafeMath
 * Casts strings for processing 256-bit integers in PHP
 */
class SafeMath {
	public static function add(string $a, string $b) {
		$c = floor($a + $b);
		if (!($c >= $a)) throw new Exception('adding values failed');
		return $c;
	}

	public static function sub(string $a, string $b) {
		if (!($b <= $a)) throw new Exception('subtracting values failed');
		return floor($a - $b);
	}

	public static function div(string $a, string $b) {
		$c = floor($a / $b);
		return $c;
	}

	public static function mul(string $a, string $b) {
		$c = floor($a * $b);
		if (!($a == 0 || $c / $a == $b)) throw new Exception('multiplying values failed');
		return $c;
	}
}