<?php
declare(strict_types=1);

namespace EnjinCoin\Test\Util;

use PHPUnit\Framework\TestCase;
use EnjinCoin\Util\SafeMath;
use PHPUnit\Runner\Exception;

/**
 * @covers \EnjinCoin\Util\SafeMath
 */
final class SafeMathTest extends TestCase {

	public $largeNum = '999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999';

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

	public function testMul_Zero(): void {
		self::assertEquals(0, SafeMath::mul('0', '10'));
		self::assertEquals(0, SafeMath::mul('10', '0'));
	}

	public function testMul_Invalid(): void {
		$this->expectException(Exception::class);
		SafeMath::mul($this->largeNum, $this->largeNum);
	}

}
