<?php
namespace EnjinCoin\Util;

class Rlp {
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

	public static function safeParseInt($v, $base) {
		if (substr($v, 0, 2) === '00') {
			throw new Exception('invalid RLP: extra zeros');
		}
		return parseInt($v, $base);
	}

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

	public static function decode($input, $stream) {
		if (!$input || strlen($input) === 0) {
			return new Buffer(array());
		}

		$input = toBuffer($input);
		$decoded = _decode($input);
		if ($stream) {
			return $decoded;
		}
		if (strlen($decoded['remainder']) != 0) throw new Exception('invalid remainder');
		return $decoded['data'];
	}

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

	function _decode($input) {
		$length = null;
		$llength = null;
		$data = null;
		$innerRemainder = null;
		$d = null;
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
				$d = _decode($innerRemainder);
				array_push($decoded, $d->data);
				$innerRemainder = $d->remainder;
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
				$d = _decode($innerRemainder);
				array_push($decoded, $d->data);
				$innerRemainder = $d->remainder;
			}
			return array("data" => $decoded, "remainder" => substr($input, $totalLength));
		}
	}

	function isHexPrefixed($str) {
		return substr($str, 0, 2) === '0x';
	}

	function stripHexPrefix($str) {
		if (gettype($str) !== 'string') {
			return $str;
		}
		return (isHexPrefixed($str)) ? substr($str, 2) : $str;
	}

	function intToHex($i) {
		$hex = (16);
		if (count($hex) % 2) {
			$hex = '0' + $hex;
		}
		return $hex;
	}

	function padToEven($a) {
		if (count($a) % 2) {
			$a = '0' + $a;
		}
		return $a;
	}

	function intToBuffer($i) {
		$hex = intToHex($i);
		return new Buffer($hex, 'hex');
	}

	function toBuffer($v) {
		if (!Buffer::isBuffer($v)) {
			if (gettype($v) === 'string') {
				if (isHexPrefixed($v)) {
					$v = new Buffer(padToEven(stripHexPrefix($v)), 'hex');
				} else {
					$v = new Buffer($v);
				}
			} else if (gettype($v) === 'number') {
				if (!$v) {
					$v = new Buffer(array());
				} else {
					$v = intToBuffer($v);
				}
			} else if ($v === null/* || $v === $undefined*/) {
				$v = new Buffer(array());
			} else if ($v->toArray) {
				$v = new Buffer($v->toArray());
			} else {
			}
		}
		return $v;
	}
}