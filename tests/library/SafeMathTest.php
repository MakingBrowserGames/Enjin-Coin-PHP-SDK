<?php
declare(strict_types=1);
namespace EnjinCoin;
use PHPUnit\Framework\TestCase;

/**
 * @covers SafeMath
 */
final class SafeMathTest extends TestCase
{
	public function testAdd(): void
	{
		$this->assertEquals(
			'30000000000000000001',
			SafeMath::add('10000000000000000000', '20000000000000000001')
		);
	}

	public function testSub(): void
	{
		$this->assertEquals(
			'10000000000000000001',
			SafeMath::sub('20000000000000000001', '10000000000000000000')
		);
	}

	public function testDiv(): void
	{
		$this->assertEquals(
			'1000000000000000000',
			SafeMath::div('20000000000000000000', '20')
		);
	}

	public function testMul(): void
	{
		$this->assertEquals(
			'1000000000000000000',
			SafeMath::div('20000000000000000000', '20')
		);
	}
}
