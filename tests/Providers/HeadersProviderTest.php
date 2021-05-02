<?php

namespace Providers;

use Crossview\Exphpress\Providers\HeadersProvider;
use PHPUnit\Framework\TestCase;

class HeadersProviderTest extends TestCase
{
	public function testConstructorCallsParentWithDefaultHeaders(): void
	{
		$provider = new HeadersProvider( [ 'the question' => 42 ] );
		$this->assertEquals( 42, $provider->getRaw( 'the question' ) );
	}

	public function testUnsetRemovedHeaderFromValues(): void
	{
		$provider = new HeadersProvider( [ 'the question' => 42 ] );
		$provider->unset( 'the question' );

		$this->assertNull( $provider->get( 'the question' ) );
	}

	public function testUnsetReturnsHeadersProvider(): void
	{
		$provider = new HeadersProvider( [] );
		$result   = $provider->unset( 'the question' ); // Doesn't throw because unset doesn't care if a symbol exists

		$this->assertInstanceOf( HeadersProvider::class, $result );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendHeadersSendsHeaders(): void
	{
		$provider = new HeadersProvider( [ 'Location' => 'https://oseiskar.github.io/black-hole/' ] );
		$provider->sendHeaders();
		$headers = xdebug_get_headers();

		$this->assertCount( 1, $headers );
		$this->assertContains( 'Location: https://oseiskar.github.io/black-hole/', $headers );
	}
}
