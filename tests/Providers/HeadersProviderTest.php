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

	/**
	 * @runInSeparateProcess
	 */
	public function testSendHeadersFlushesHeaderBuffer(): void
	{
		$provider = new HeadersProvider(['Location' => 'https://oseiskar.github.io/black-hole/']);
		$provider->sendHeaders();

		$this->assertNull($provider->get('Location'));
	}
}
