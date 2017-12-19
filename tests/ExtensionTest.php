<?php
declare(strict_types=1);

namespace EnjinCoin\Test;

use PHPUnit\Framework\TestCase;

/**
 * @covers Config
 */
final class ExtensionTest extends TestCase {

	public function testHasCorrectExtensionsLoaded(): void {
		$this->assertEquals(true, extension_loaded('curl'));
		$this->assertEquals(true, extension_loaded('mysqli'));
		$this->assertEquals(true, extension_loaded('sockets'));
		$this->assertEquals(true, extension_loaded('mbstring'));
	}

}
