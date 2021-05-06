<?php

namespace Providers;

use Crossview\Exphpress\Providers\CrudArrayValueProvider;
use PHPUnit\Framework\TestCase;

class CrudArrayValueProviderTest extends TestCase
{
	private CrudArrayValueProvider $provider;

	protected function setUp(): void
	{
		$this->provider = new CrudArrayValueProvider( [ 'the question' => 42 ] );
	}

	public function testConstructorCallsParentWithDefaultHeaders(): void
	{
		$this->assertEquals( 42, $this->provider->getRaw( 'the question' ) );
	}

	public function testUnsetRemovedHeaderFromValues(): void
	{
		$this->provider->unset( 'the question' );

		$this->assertNull( $this->provider->get( 'the question' ) );
	}

	public function testUnsetReturnsHeadersProvider(): void
	{
		$result = $this->provider->unset( 'the question' ); // Doesn't throw because unset doesn't care if a symbol exists

		$this->assertInstanceOf( CrudArrayValueProvider::class, $result );
	}

	public function testUnsetAllUnsetsAllValues(): void
	{
		$this->provider->unsetAll();
		$this->assertNull( $this->provider->get( 'the question' ) );
	}
}
