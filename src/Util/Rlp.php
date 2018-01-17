<?php
namespace EnjinCoin\Util;

/**
 * Class Rlp
 * @package EnjinCoin\Util
 */
class Rlp {

	/**
	 * Function to encode
	 * @param $input
	 * @return mixed
	 */
	public static function encode($input) {
		if (is_array($input)) {
			$output = array();
			for ($i = 0; $i < count($input); $i++) {
				array_push($output, self::encode($input[$i]));
			}
			$buf = Buffer::concat($output);
			return Buffer::concat(array(encodeLength(count($buf), 192), $buf));
		} else {
			$input = toBuffer($input);
			if (count($input) === 1 && $input[0] < 128) {
				return $input;
			} else {
				return Buffer::concat(array(encodeLength(count($input), 128), $input));
			}
		}
	}

	/**
	 * @param $strVal
	 * @param $base
	 * @throws Exception if invalid rlp passed in
	 * @return mixedFunction to parse an int
	 */
	public static function safeParseInt($strVal, $base) {
		if (substr($strVal, 0, 2) === '00') {
			throw new Exception('invalid RLP: extra zeros');
		}
		return parseInt($strVal, $base);
	}

	/**
	 * Function to encode the length
	 * @param $len
	 * @param $offset
	 * @return Buffer
	 */
	public static function encodeLength($len, $offset) {
		if ($len < 56) {
			return new Buffer(array($len + $offset));
		} else {
			$hexLength = intToHex($len);
			$lLength = count($hexLength) / 2;
			$firstByte = intToHex($offset + 55 + $lLength);
			return new Buffer($firstByte + $hexLength, 'hex');
		}
	}

	/**
	 * Function to decode
	 * @param $input
	 * @param $stream
	 * @throws Exception if invalid remainder
	 * @return Buffer
	 */
	public static function decode($input, $stream) {
		if (!$input || strlen($input) === 0) {
			return new Buffer(array());
		}

		$input = toBuffer($input);
		$decoded = _decode($input);
		if ($stream) {
			return $decoded;
		}
		if (strlen($decoded['remainder']) !== 0) {
			throw new Exception('invalid remainder');
		}
		return $decoded['data'];
	}

	/**
	 * Function to get length
	 * @param $input
	 * @return Buffer|int
	 */
	public static function getLength($input) {
		if (!$input || count($input) === 0) {
			return new Buffer(array());
		}
		$input = toBuffer($input);
		$firstByte = $input[0];
		if ($firstByte <= 0x7f) {
			return count($input);
		} else if ($firstByte <= 0xb7) {
			return $firstByte - 0x7f;
		} else if ($firstByte <= 0xbf) {
			return $firstByte - 0xb6;
		} else if ($firstByte <= 0xf7) {
			return $firstByte - 0xbf;
		} else {
			$llength = $firstByte - 0xf6;
			$length = safeParseInt(('hex'), 16);
			return $llength + $length;
		}
	}

	/**
	 * Function to decode
	 * @param $input
	 * @return array
	 */
	private function _decode($input) {
		$length = null;
		$llength = null;
		$data = null;
		$innerRemainder = null;
		$dVal = null;
		$decoded = array();
		$firstByte = $input[0];
		if ($firstByte <= 0x7f) {
			return array("data" => substr($input, 0, 1), "remainder" => substr($input, 1));
		} else if ($firstByte <= 0xb7) {
			$length = $firstByte - 0x7f;
			if ($firstByte === 0x80) {
				$data = new Buffer(array());
			} else {
				$data = substr($input, 1, $length);
			}
			if ($length === 2 && $data[0] < 0x80) {
			}
			return array("data" => $data, "remainder" => substr($input, $length));
		} else if ($firstByte <= 0xbf) {
			$llength = $firstByte - 0xb6;
			$length = safeParseInt(('hex'), 16);
			$data = substr($input, $llength, $length + $llength);
			if (count($data) < $length) {
			}
			return array("data" => $data, "remainder" => substr($input, $length + $llength));
		} else if ($firstByte <= 0xf7) {
			$length = $firstByte - 0xbf;
			$innerRemainder = substr($input, 1, $length);
			while (count($innerRemainder)) {
				$dVal = _decode($innerRemainder);
				array_push($decoded, $dVal->data);
				$innerRemainder = $dVal->remainder;
			}
			return array("data" => $decoded, "remainder" => substr($input, $length));
		} else {
			$llength = $firstByte - 0xf6;
			$length = safeParseInt(('hex'), 16);
			$totalLength = $llength + $length;
			if ($totalLength > count($input)) {
			}
			$innerRemainder = substr($input, $llength, $totalLength);
			if (count($innerRemainder) === 0) {
			}
			while (count($innerRemainder)) {
				$dVal = _decode($innerRemainder);
				array_push($decoded, $dVal->data);
				$innerRemainder = $dVal->remainder;
			}
			return array("data" => $decoded, "remainder" => substr($input, $totalLength));
		}
	}

	/**
	 * Function to check if hex is prefixed
	 * @param $str
	 * @return bool
	 */
	function isHexPrefixed($str) {
		return substr($str, 0, 2) === '0x';
	}

	/**
	 * Function to strip the hex prefix
	 * @param $str
	 * @return bool|string
	 */
	function stripHexPrefix($str) {
		if (gettype($str) !== 'string') {
			return $str;
		}
		return (isHexPrefixed($str)) ? substr($str, 2) : $str;
	}

	/**
	 * @param $intVal
	 * @return int|stringFUnction to convert int to hex
	 */
	function intToHex($intVal) {
		$hex = (16);
		if (count($hex) % 2) {
			$hex = '0' + $hex;
		}
		return $hex;
	}

	/**
	 * Function to pad to even
	 * @param $var
	 * @return string
	 */
	function padToEven($var) {
		if (count($var) % 2) {
			$var = '0' + $var;
		}
		return $var;
	}

	/**
	 * Function to convert int to buffer
	 * @param $intVal
	 * @return Buffer
	 */
	function intToBuffer($intVal) {
		$hex = intToHex($intVal);
		return new Buffer($hex, 'hex');
	}

	/**
	 * Function to convert to buffer
	 * @param $var
	 * @return Buffer
	 */
	function toBuffer($var) {
		if (!Buffer::isBuffer($var)) {
			if (gettype($var) === 'string') {
				if (isHexPrefixed($var)) {
					$var = new Buffer(padToEven(stripHexPrefix($var)), 'hex');
				} else {
					$var = new Buffer($var);
				}
			} else if (gettype($var) === 'number') {
				if (!$var) {
					$var = new Buffer(array());
				} else {
					$var = intToBuffer($var);
				}
			} else if ($var === null/* || $v === $undefined*/) {
				$var = new Buffer(array());
			} else if ($var->toArray) {
				$var = new Buffer($var->toArray());
			} else {
			}
		}
		return $var;
	}
}