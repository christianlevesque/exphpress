<?php

namespace Providers;

use Crossview\Exphpress\Providers\CrudArrayValueProvider;
use PHPUnit\Framework\TestCase;

class CrudArrayValueProviderTest extends TestCase
{
	public function testConstructorCallsParentWithDefaultHeaders(): void
	{
		$provider = new CrudArrayValueProvider( [ 'the question' => 42 ] );
		$this->assertEquals( 42, $provider->getRaw( 'the question' ) );
	}

	public function testUnsetRemovedHeaderFromValues(): void
	{
		$provider = new CrudArrayValueProvider( [ 'the question' => 42 ] );
		$provider->unset( 'the question' );

		$this->assertNull( $provider->get( 'the question' ) );
	}

	public function testUnsetReturnsHeadersProvider(): void
	{
		$provider = new CrudArrayValueProvider( [] );
		$result   = $provider->unset( 'the question' ); // Doesn't throw because unset doesn't care if a symbol exists

		$this->assertInstanceOf( CrudArrayValueProvider::class, $result );
	}
}
