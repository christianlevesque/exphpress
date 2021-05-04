<?php

namespace Http;

use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Providers\CookieProvider;
use Crossview\Exphpress\Providers\HeadersProvider;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
	private Response        $response;
	private HeadersProvider $headersProvider;
	private CookieProvider  $cookieProvider;

	public function setUp(): void
	{
		$this->response        = new Response();
		$this->headersProvider = $this->createMock( HeadersProvider::class );
		$this->cookieProvider  = $this->createMock( CookieProvider::class );
		$this->response->setHeadersProvider( $this->headersProvider );
		$this->response->setCookieProvider( $this->cookieProvider );
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
		$response = new Response();
		$response->setHeadersProvider( $this->headersProvider );
		$this->assertInstanceOf( HeadersProvider::class, $this->response->getHeadersProvider() );
	}

	public function testSetHeadersProviderReturnsResponse(): void
	{
		$response = new Response();
		$this->assertInstanceOf( Response::class, $response->setHeadersProvider( $this->headersProvider ) );
	}

	public function testSetHeadersProviderThrowsIfHeadersProviderExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to set the Response HeadersProvider, but a HeadersProvider has already been configured.' );
		$this->response->setHeadersProvider( $this->headersProvider );
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
		$response = new Response();
		$response->setCookieProvider( $this->cookieProvider );
		$this->assertInstanceOf( CookieProvider::class, $this->response->getCookieProvider() );
	}

	public function testSetCookieProviderReturnsResponse(): void
	{
		$response = new Response();
		$this->assertInstanceOf( Response::class, $response->setCookieProvider( $this->cookieProvider ) );
	}

	public function testSetCookieProviderThrowsIfCookieProviderExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to set the Response CookieProvider, but a CookieProvider has already been configured.' );
		$this->response->setCookieProvider( $this->cookieProvider );
	}

	// setHeader
	public function testSetHeaderSetsHeader(): void
	{
		$name  = 'X-Powered-By';
		$value = 'coffee and PHP';
		$this->headersProvider->expects( $this->once() )
							  ->method( 'setHeader' )
							  ->with( $name, $value );
		$this->response->setHeader( $name, $value );
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

	// unsetHeader
	public function testUnsetHeaderUnsetsHeader(): void
	{
		$name = 'X-Powered-By';
		$this->headersProvider->expects( $this->once() )
							  ->method( 'unsetHeader' )
							  ->with( $name );
		$this->response->unsetHeader( $name );
	}

	public function testUnsetHeaderReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->unsetHeader( '' ) );
	}

	public function testUnsetHeaderThrowsIfHeaderProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response HeadersProvider, but none has been configured.' );
		$response->unsetHeader( '' );
	}

	// setCookie
	public function testSetCookieSetsCookie(): void
	{
		$name  = 'auth_token';
		$value = 'abc123';
		$this->cookieProvider->expects( $this->once() )
							 ->method( 'setCookie' )
							 ->with( $name, $value );
		$this->response->setCookie( $name, $value );
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
		$name = 'my_cookie';
		$this->cookieProvider->expects( $this->once() )
							 ->method( 'unsetCookie' )
							 ->with( $name );
		$this->response->unsetCookie( $name );
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

	// deleteCookie
	public function testDeleteCookieDeletesCookie(): void
	{
		$name = 'my_cookie';
		$this->cookieProvider->expects( $this->once() )
							 ->method( 'deleteCookie' )
							 ->with( $name, [] );
		$this->response->deleteCookie( $name );
	}

	public function testDeleteCookieReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->deleteCookie( '' ) );
	}

	public function testDeleteCookieThrowsIfCookieProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response CookieProvider, but none has been configured.' );
		$response->deleteCookie( '' );
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
	public function testSendHeadersSendsHeaders(): void
	{
		$this->headersProvider->expects( $this->once() )
							  ->method( 'sendHeaders' );
		$this->response->sendHeaders();
	}

	public function testSendHeadersReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->sendHeaders() );
	}

	public function testSendHeadersThrowsIfHeadersProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response HeadersProvider, but none has been configured.' );
		$response->sendHeaders();
	}

	//sendCookies
	public function testSendCookiesSendsCookies(): void
	{
		$this->cookieProvider->expects( $this->once() )
							 ->method( 'sendCookies' );
		$this->response->sendCookies();
	}

	public function testSendCookiesReturnsResponse(): void
	{
		$this->assertInstanceOf( Response::class, $this->response->sendCookies() );
	}

	public function testSendCookiesThrowsIfCookiesProviderNotExists(): void
	{
		$response = new Response();
		$this->expectErrorMessage( 'You are attempting to access the Response CookieProvider, but none has been configured.' );
		$response->sendCookies();
	}

	// send
	public function testSendCallsSendHttpStatus(): void
	{
		$this->response->status( 413 )
					   ->send();
		$this->assertEquals( 413, http_response_code() );
	}

	public function testSendCallsSendCookies(): void
	{
		$this->cookieProvider->expects( $this->once() )
							 ->method( 'sendCookies' );
		$this->response->send();
	}

	public function testSendCallsSendHeaders(): void
	{
		$this->headersProvider->expects( $this->once() )
							  ->method( 'sendHeaders' );
		$this->response->unsetCookie( 'my_cookie' )
					   ->send();
	}

	public function testSendEchoesResponseBody(): void
	{
		ob_start();
		$this->response->setResponseBody( 'Hello Exphpress!' )
					   ->send();
		$output = ob_get_clean();

		$this->assertEquals( 'Hello Exphpress!', $output );
	}

	public function testSendReplacesBodyIfReplaceBodyIsTrue(): void
	{
		$this->response->setResponseBody( 'Hello Exphpress!' );
		ob_start();
		$this->response->send( 'Hello PHP!', true );
		$output = ob_get_clean();

		$this->assertEquals( 'Hello PHP!', $output );
	}

	public function testSendAppendstoBodyIfReplaceBodyIsFalse(): void
	{
		$this->response->setResponseBody( 'Hello Exphpress!' );
		ob_start();
		$this->response->send( ' Hello PHP!' );
		$output = ob_get_clean();

		$this->assertEquals( 'Hello Exphpress! Hello PHP!', $output );
	}
}
