<?php

namespace Providers;

use Crossview\Exphpress\Providers\ArrayValueProvider;
use PHPUnit\Framework\TestCase;

class ArrayValueProviderTest extends TestCase
{
	private ArrayValueProvider $provider;

	protected function setUp(): void
	{
		$values = [
			'value1' => 'a string',
			'value2' => 42
		];

		$this->provider = new ArrayValueProvider( $values );
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( ArrayValueProvider::class, $this->provider );
	}

	public function testGetReturnsValueIfExists(): void
	{
		$result = $this->provider->get( 'value1' );
		$this->assertIsString( $result );
		$this->assertEquals( 'a string', $result );
	}

	public function testGetReturnsStringOfNonString(): void
	{
		$result = $this->provider->get( 'value2' );
		$this->assertIsString( $result );
		$this->assertEquals( '42', $result );
	}

	public function testGetReturnsNullifNotExists(): void
	{
		$result = $this->provider->get( 'value3' );
		$this->assertNull( $result );
	}

	public function testGetRawReturnsValueIfExists(): void
	{
		$result = $this->provider->getRaw( 'value1' );
		$this->assertIsString( $result );
		$this->assertEquals( 'a string', $result );
	}

	public function testGetRawReturnsOriginalType(): void
	{
		$result = $this->provider->getRaw( 'value2' );
		$this->assertIsNumeric( $result );
		$this->assertEquals( 42, $result );
	}

	public function testGetRawReturnsNullifNotExists(): void
	{
		$result = $this->provider->getRaw( 'value3' );
		$this->assertNull( $result );
	}
}
