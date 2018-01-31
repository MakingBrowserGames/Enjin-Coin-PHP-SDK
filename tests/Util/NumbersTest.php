<?php
declare(strict_types=1);

namespace EnjinCoin\Test\Util;

use PHPUnit\Framework\TestCase;
use EnjinCoin\Util\Numbers;

/**
 * @covers \EnjinCoin\Util\Numbers
 */
final class NumbersTest extends TestCase {
	public function testDecodeHex_Success1(): void {
		$hex = "0x00000000";
		$result = Numbers::decodeHex($hex);
		$this->assertNotNull($result);
		$this->assertSame('0', $result);
	}

	public function testDecodeHex_Success2(): void {
		$hex = "XXXXXX";
		$result = Numbers::decodeHex($hex);
		$this->assertNotNull($result);
		$this->assertSame($hex, $result);
	}

	public function testEncodeHex_Success1(): void {
		$input = "0x00000000";
		$result = Numbers::encodeHex($input);
		$this->assertNotNull($result);
		$this->assertSame('0', $result);
	}

	public function testEncodeHex_Success2(): void {
		$input = "XXXXXX";
		$result = Numbers::encodeHex($input);
		$this->assertNotNull($input, $result);
	}
}
