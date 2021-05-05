<?php

namespace Utilities;

use PHPUnit\Framework\TestCase;
use Crossview\Exphpress\Utilities\CanProcessPaths;

class CanProcessPathsTest extends TestCase
{
	private $unit;

	protected function setUp(): void
	{
		$this->unit = $this->getMockForTrait( CanProcessPaths::class );
	}

	public function testProcessPathReturnsParsedArray(): void
	{
		$processed = $this->unit->processPath( '/some/url' );

		$this->assertCount( 2, $processed );
		$this->assertEquals( 'some', $processed[ 0 ] );
		$this->assertEquals( 'url', $processed[ 1 ] );
	}

	public function testProcessPathReturnsEmptyArrayIfPathEmpty(): void
	{
		$processed = $this->unit->processPath( '' );
		$this->assertCount( 0, $processed );
	}

	public function testProcessPathReturnsEmptyArrayIfNotPath(): void
	{
		$processed = $this->unit->processPath( 'not-a-path' );
		$this->assertCount( 0, $processed );
	}
}
