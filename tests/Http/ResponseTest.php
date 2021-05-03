<?php

namespace Http;

use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Providers\CookieProvider;
use Crossview\Exphpress\Providers\HeadersProvider;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
	private Response $response;

	public function setUp(): void
	{
		$this->response = new Response();
		$this->response->setHeadersProvider( new HeadersProvider( [ 'X-Some-Header' => 42 ] ) );
		$this->response->setCookieProvider( new CookieProvider(
			[],
			[
				'my_cookie' => [
					'value'   => 'is tasty',
					'options' => []
				]
			]
		) );
	}

	// Constructor
	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( Response::class, $this->response );
	}

	// responseCode getter/setters
	public function testGetResponseCodeReturnsResponseCode(): void
	{
		$this->assertEquals( 200, $this->response->getResponseCode() );
	}

	public function testSetResponseCodeSetsResponseCode(): void
	{
		$expected = 413;
		$this->response->setResponseCode( $expected );
		$this->assertEquals( $expected, $this->response->getResponseCode() );
	}

	public function testSetResponseCodeReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->setResponseCode( 500 ) );
	}

	public function testStatusSetsResponseCode(): void
	{
		$expected = 413;
		$this->response->status( $expected );
		$this->assertEquals( $expected, $this->response->getResponseCode() );
	}

	// responseBody getter/setters
	public function testGetResponseBodyReturnsResponseBody(): void
	{
		$result = $this->response->getResponseBody();
		$this->assertEmpty( $result );
		$this->assertEquals( '', $result );
	}

	public function testSetResponseBodySetsResponseBody(): void
	{
		$expected = 'This is a response body';
		$this->response->setResponseBody( $expected );
		$this->assertEquals( $expected, $this->response->getResponseBody() );
	}

	public function testSetResponseBodyReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->setResponseBody( '' ) );
	}

	public function testAppendToResponseBodyAppendsToResponseBody(): void
	{
		$initial = 'Hello';
		$this->response->setResponseBody( $initial );
		$this->assertEquals( $initial, $this->response->getResponseBody() );

		$addendum = ' World!';
		$this->response->appendToResponseBody( $addendum );

		$this->assertEquals( "$initial$addendum", $this->response->getResponseBody() );
	}

	public function testAppendToResponseBodyReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->appendToResponseBody( '' ) );
	}

	// headersProvider getter/setter
	public function testGetHeadersProviderReturnsHeadersProvider(): void
	{
		$this->assertInstanceOf( HeadersProvider::class, $this->response->getHeadersProvider() );
	}

	public function testGetHeadersProviderThrowsIfHeadersProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response HeadersProvider, but none has been configured.' );
		$response->getHeadersProvider();
	}

	public function testSetHeadersProviderSetsHeadersProvider(): void
	{
		$result = $this->response->getHeadersProvider();
		$this->assertEquals( 42, $result->getHeader( 'X-Some-Header' ) );
	}

	public function testSetHeadersProviderReturnsResponse(): void
	{
		$response = new Response();
		$this->assertInstanceOf( Response::class, $response->setHeadersProvider( new HeadersProvider() ) );
	}

	public function testSetHeadersProviderThrowsIfHeadersProviderExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to set the Response HeadersProvider, but a HeadersProvider has already been configured.' );
		$this->response->setHeadersProvider( new HeadersProvider() );
	}

	// cookieProvider getter/setter
	public function testGetCookieProviderReturnsCookieProvider(): void
	{
		$this->assertInstanceOf( CookieProvider::class, $this->response->getCookieProvider() );
	}

	public function testGetCookieProviderThrowsIfCookieProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response CookieProvider, but none has been configured.' );
		$response->getCookieProvider();
	}

	public function testSetCookieProviderSetsCookieProvider(): void
	{
		$provider = $this->response->getCookieProvider();
		$this->assertEquals( 'is tasty', $provider->getCookie( 'my_cookie' )[ 'value' ] );
	}

	public function testSetCookieProviderReturnsResponse(): void
	{
		$response = new Response();
		$this->assertInstanceOf( Response::class, $response->setCookieProvider( new CookieProvider() ) );
	}

	public function testSetCookieProviderThrowsIfCookieProviderExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to set the Response CookieProvider, but a CookieProvider has already been configured.' );
		$this->response->setCookieProvider( new CookieProvider() );
	}

	// setHeader
	public function testSetHeaderSetsHeader(): void
	{
		$name  = 'X-Powered-By';
		$value = 'coffee and PHP';
		$this->response->setHeader( $name, $value );
		$result = $this->response->getHeadersProvider()
								 ->getHeader( $name );
		$this->assertEquals( $value, $result );
	}

	public function testSetHeaderReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->setHeader( '', '' ) );
	}

	public function testSetHeaderThrowsIfHeaderProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response HeadersProvider, but none has been configured.' );
		$response->setHeader( '', '' );
	}

	// setCookie
	public function testSetCookieSetsCookie(): void
	{
		$name  = 'auth_token';
		$value = 'abc123';
		$this->response->setCookie( $name, $value );

		$result = $this->response->getCookieProvider()
								 ->getCookie( $name );

		$this->assertNotNull( $result );
		$this->assertEquals( $value, $result[ 'value' ] );
	}

	public function testSetCookieReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->setCookie( '', '' ) );
	}

	public function testSetCookieThrowsIfCookieProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response CookieProvider, but none has been configured.' );
		$response->setCookie( '', '' );
	}

	// unsetCookie
	public function testUnsetCookieUnsetsCookie(): void
	{
		$this->response->unsetCookie( 'my_cookie' );
		$this->assertNull( $this->response->getCookieProvider()
										  ->getCookie( 'my_cookie' ) );
	}

	public function testUnsetCookieReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->unsetCookie( 'my_cookie' ) );
	}

	public function testUnsetCookieThrowsIfCookieProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response CookieProvider, but none has been configured.' );
		$response->setCookie( '', '' );
	}

	// sendHttpStatus
	public function testSendHttpStatusSendsStatusCode(): void
	{
		$this->response->status( 413 )
					   ->sendHttpStatus();
		$this->assertEquals( 413, http_response_code() );
	}

	public function testSendHttpStatusReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->sendHttpStatus() );
	}

	// sendHeaders

	/**
	 * @runInSeparateProcess
	 */
	public function testSendHeadersSendsHeaders(): void
	{
		$this->response->sendHeaders();
		$headers = xdebug_get_headers();
		$this->assertContains( 'X-Some-Header: 42', $headers );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendHeadersReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->sendHeaders() );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendHeadersThrowsIfHeadersProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response HeadersProvider, but none has been configured.' );
		$response->sendHeaders();
	}

	//sendCookies

	/**
	 * @runInSeparateProcess
	 */
	public function testSendCookiesSendsCookies(): void
	{
		$this->response->sendCookies();
		$headers = xdebug_get_headers();
		$this->assertStringStartsWith( 'Set-Cookie: my_cookie', $headers[ 0 ] );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendCookiesReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->sendCookies() );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendCookiesThrowsIfCookiesProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response CookieProvider, but none has been configured.' );
		$response->sendCookies();
	}

	// send

	/**
	 * @runInSeparateProcess
	 */
	public function testSendCallsSendHttpStatus(): void
	{
		$this->response->status( 413 )
					   ->send();
		$this->assertEquals( 413, http_response_code() );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendCallsSendCookies(): void
	{
		$this->response->getHeadersProvider()
					   ->unsetHeader( 'X-Some-Header' );
		$this->response->send();
		$headers = xdebug_get_headers();
		$this->assertCount( 1, $headers );
		$this->assertStringStartsWith( 'Set-Cookie: my_cookie', $headers[ 0 ] );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendCallsSendHeaders(): void
	{
		$this->response->unsetCookie( 'my_cookie' )
					   ->send();
		$headers = xdebug_get_headers();
		$this->assertCount( 1, $headers );
		$this->assertEquals( 'X-Some-Header: 42', $headers[ 0 ] );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendEchoesResponseBody(): void
	{
		ob_start();
		$this->response->setResponseBody( 'Hello Exphpress!' )
					   ->send();
		$output = ob_get_clean();

		$this->assertEquals( 'Hello Exphpress!', $output );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendReplacesBodyIfReplaceBodyIsTrue(): void
	{
		$this->response->setResponseBody( 'Hello Exphpress!' );
		ob_start();
		$this->response->send( 'Hello PHP!', true );
		$output = ob_get_clean();

		$this->assertEquals( 'Hello PHP!', $output );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendAppendstoBodyIfReplaceBodyIsFalse(): void
	{
		$this->response->setResponseBody( 'Hello Exphpress!' );
		ob_start();
		$this->response->send( ' Hello PHP!' );
		$output = ob_get_clean();

		$this->assertEquals( 'Hello Exphpress! Hello PHP!', $output );
	}
}
