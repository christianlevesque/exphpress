<?php

namespace Providers;

use Crossview\Exphpress\Providers\HeadersProvider;
use PHPUnit\Framework\TestCase;

class HeadersProviderTest extends TestCase
{
	private HeadersProvider $provider;

	protected function setUp(): void
	{
		$this->provider = new HeadersProvider( [ 'Location' => 'https://oseiskar.github.io/black-hole/' ] );
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( HeadersProvider::class, $this->provider );
	}

	public function testConstructorPopulatesHeadersArrayWithDefaultHeaders(): void
	{
		$this->assertEquals( 'https://oseiskar.github.io/black-hole/', $this->provider->getHeader( 'Location' ) );
	}

	public function testGetHeaderReturnsHeaderValue(): void
	{
		$result = $this->provider->getHeader( 'Location' );
		$this->assertIsString( $result );
		$this->assertEquals( 'https://oseiskar.github.io/black-hole/', $result );
	}

	public function testGetHeaderReturnsNullIfHeaderNotExists(): void
	{
		$this->assertNull( $this->provider->getHeader( 'X-Powered-By' ) );
	}

	public function testSetHeaderSetsHeader(): void
	{
		$name  = 'X-Powered-By';
		$value = 'PHP/Nginx';
		$this->provider->setHeader( $name, $value );

		$this->assertEquals( $value, $this->provider->getHeader( $name ) );
	}

	public function testSetHeaderReturnsHeadersProvider(): void
	{
		$result = $this->provider->setHeader( '', '' );
		$this->assertInstanceOf( HeadersProvider::class, $result );
	}

	public function testUnsetHeaderRemovesHeaderFromQueue(): void
	{
		$this->provider->unsetHeader( 'Location' );
		$this->assertNull( $this->provider->getHeader( 'Location' ) );
	}

	public function testUnsetHeaderReturnsHeadersProvider(): void
	{
		$result = $this->provider->unsetHeader( 'Location' );
		$this->assertInstanceOf( HeadersProvider::class, $result );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendHeadersSendsHeaders(): void
	{
		$this->provider->sendHeaders();
		$headers = xdebug_get_headers();

		$this->assertCount( 1, $headers );
		$this->assertContains( 'Location: https://oseiskar.github.io/black-hole/', $headers );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendHeadersFlushesHeaderBuffer(): void
	{
		$this->provider->sendHeaders();

		$this->assertNull( $this->provider->getHeader( 'Location' ) );
	}
}
