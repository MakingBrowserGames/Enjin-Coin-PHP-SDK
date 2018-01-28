<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use PHPUnit\Framework\TestCase;
use EnjinCoin\Util\SafeMath;

/**
 * @covers \EnjinCoin\Util\SafeMath
 */
final class SafeMathTest extends TestCase {
	public function testAdd(): void {
		$this->assertEquals(
			'30000000000000000001',
			SafeMath::add('10000000000000000000', '20000000000000000001')
		);
	}

	/**
	 * @expectedException Exception
	 */
	public function testAdd_failed(): void {
		$result = SafeMath::add('10000000000000000000', '-20000000000000000001');
		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('adding values failed');

	}

	public function testSub(): void {
		$this->assertEquals(
			'10000000000000000001',
			SafeMath::sub('20000000000000000001', '10000000000000000000')
		);
	}

	/**
	 * @expectedException Exception
	 */
	public function testSubfailed(): void {
		$result = SafeMath::sub('10000000000000000000', '20000000000000000001');
		$this->assertEmpty($result);
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('subtracting values failed');

	}

	public function testDiv(): void {
		$this->assertEquals(
			'952380952380952380',
			SafeMath::div('20000000000000000000', '21')
		);
	}

	public function testMul(): void {
		$this->assertEquals(
			'420000000000000000000',
			SafeMath::mul('20000000000000000000', '21')
		);
	}

}
