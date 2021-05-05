<?php

namespace Http;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Providers\ArrayValueProvider;
use Crossview\Exphpress\Providers\WritableArrayValueProvider;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
	private Request                    $request;
	private ArrayValueProvider         $serverProvider;
	private ArrayValueProvider         $cookieProvider;
	private WritableArrayValueProvider $queryParameterProvider;
	private WritableArrayValueProvider $requestParameterProvider;

	protected function setUp(): void
	{
		$this->request                  = new Request( '/default/path' );
		$this->serverProvider           = $this->createMock( ArrayValueProvider::class );
		$this->cookieProvider           = $this->createMock( ArrayValueProvider::class );
		$this->queryParameterProvider   = $this->createMock( WritableArrayValueProvider::class );
		$this->requestParameterProvider = $this->createMock( WritableArrayValueProvider::class );
		$this->request->setServerProvider( $this->serverProvider );
		$this->request->setCookieProvider( $this->cookieProvider );
		$this->request->setQueryParameterProvider( $this->queryParameterProvider );
		$this->request->setRequestParameterProvider( $this->requestParameterProvider );
	}

	// constructor
	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( Request::class, $this->request );
	}

	// getServerProvider
	public function testGetServerProviderReturnsServerProviderIfExists(): void
	{
		$this->assertInstanceOf( ArrayValueProvider::class, $this->request->getServerProvider() );
	}

	public function testGetServerProviderThrowsIfNotExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to access the Request ServerProvider, but none has been configured.' );
		$request = new Request( '' );
		$request->getServerProvider();
	}

	// setServerProvider
	public function testSetServerProviderThrowsIfAlreadyExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to set the Request ServerProvider, but a ServerProvider has already been configured.' );
		$this->request->setServerProvider( new ArrayValueProvider( [] ) );
	}

	// getQueryParameterProvider
	public function testGetQueryParameterProviderReturnsQueryParameterProviderIfExists(): void
	{
		$this->assertInstanceOf( WritableArrayValueProvider::class, $this->request->getQueryParameterProvider() );
	}

	public function testGetQueryParameterProviderThrowsIfNotExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to access the Request QueryParameterProvider, but none has been configured.' );
		$request = new Request( '' );
		$request->getQueryParameterProvider();
	}

	// setQueryParameterProvider
	public function testSetQueryParameterProviderThrowsIfAlreadyExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to set the Request QueryParameterProvider, but a QueryParameterProvider has already been configured.' );
		$this->request->setQueryParameterProvider( new WritableArrayValueProvider( [] ) );
	}

	// getRequestParameterProvider
	public function testGetRequestParameterProviderReturnsRequestParameterProviderIfExists(): void
	{
		$this->assertInstanceOf( WritableArrayValueProvider::class, $this->request->getRequestParameterProvider() );
	}

	public function testGetRequestParameterProviderThrowsIfNotExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to access the Request RequestParameterProvider, but none has been configured.' );
		$request = new Request( '' );
		$request->getRequestParameterProvider();
	}

	// setRequestParameterProvider
	public function testSetRequestParameterProviderThrowsIfAlreadyExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to set the Request RequestParameterProvider, but a RequestParameterProvider has already been configured.' );
		$this->request->setRequestParameterProvider( new WritableArrayValueProvider( [] ) );
	}

	// getCookieProvider
	public function testGetCookieProviderReturnsCookieProviderIfExists(): void
	{
		$this->assertInstanceOf( ArrayValueProvider::class, $this->request->getCookieProvider() );
	}

	public function testGetCookieProviderThrowsIfNotExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to access the Request CookieProvider, but none has been configured.' );
		$request = new Request( '' );
		$request->getCookieProvider();
	}

	// setCookieProvider
	public function testSetCookieProviderThrowsIfAlreadyExists(): void
	{
		$this->expectErrorMessage( 'You are attempting to set the Request CookieProvider, but a CookieProvider has already been configured.' );
		$this->request->setCookieProvider( new ArrayValueProvider( [] ) );
	}

	// getOriginalUrl
	public function testGetOriginalUrlReturnsOriginalUrl(): void
	{
		$this->assertEquals( '/default/path', $this->request->getOriginalUrl() );
	}

	public function testGetMethodReturnsRequestMethodIfServerProviderSet(): void
	{
		$this->serverProvider->expects( $this->once() )
							 ->method( 'get' )
							 ->with( 'REQUEST_METHOD' )
							 ->willReturn( 'POST' );
		$result = $this->request->getMethod();

		$this->assertNotNull( $result );
		$this->assertIsString( $result );
		$this->assertEquals( 'POST', $result );
	}

	public function testGetMethodThrowsIfServerProviderNotSet(): void
	{
		$request = new Request( '' );
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$serverProvider must not be accessed before initialization' );
		$request->getMethod();
	}

	public function testGetServerParameterReturnsServerParameterIfServerProviderSet(): void
	{
		$name = 'some_value';
		$this->serverProvider->expects( $this->once() )
							 ->method( 'getRaw' )
							 ->with( $name )
							 ->willReturn( 42 );
		$result = $this->request->getServerParameter( $name );

		$this->assertIsNumeric( $result );
		$this->assertEquals( 42, $result );
	}

	public function testGetServerParameterThrowsIfServerProviderNotSet(): void
	{
		$request = new Request( '' );
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$serverProvider must not be accessed before initialization' );
		$request->getServerParameter( '' );
	}

	public function testGetQueryParameterReturnsParameterIfQueryParameterProviderSet(): void
	{
		$name  = 'id';
		$value = 42;
		$this->queryParameterProvider->expects( $this->once() )
									 ->method( 'getRaw' )
									 ->with( $name )
									 ->willReturn( $value );
		$result = $this->request->getQueryParameter( $name );

		$this->assertNotNull( $result );
		$this->assertIsNumeric( $result );
		$this->assertEquals( $value, $result );
	}

	public function testGetQueryParameterThrowsIfQueryParameterProviderNotSet(): void
	{
		$request = new Request( '' );
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$queryParameterProvider must not be accessed before initialization' );
		$request->getQueryParameter( '' );
	}

	public function testSetQueryParameterReturnsRequest(): void
	{
		$this->assertInstanceOf( Request::class, $this->request->setQueryParameter( '', true ) );
	}

	public function testSetQueryParameterSetsQueryParameterIfQueryParameterProviderSet(): void
	{
		$name     = 'the question';
		$expected = 42;
		$this->queryParameterProvider->expects( $this->once() )
									 ->method( 'set' )
									 ->with( $name, $expected );
		$this->request->setQueryParameter( $name, $expected );
	}

	public function testSetQueryParameterThrowsIfQueryParameterProviderNotSet(): void
	{
		$request = new Request( '' );
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$queryParameterProvider must not be accessed before initialization' );
		$request->setQueryParameter( '', true );
	}

	public function testGetRequestParameterReturnsParameterIfRequestParameterProviderSet(): void
	{
		$name  = 'old_id';
		$value = 84;
		$this->requestParameterProvider->expects( $this->once() )
									   ->method( 'getRaw' )
									   ->with( $name )
									   ->willReturn( $value );
		$result = $this->request->getRequestParameter( $name );

		$this->assertNotNull( $result );
		$this->assertIsNumeric( $result );
		$this->assertEquals( $value, $result );
	}

	public function testGetRequestParameterThrowsIfRequestParameterProviderNotSet(): void
	{
		$request = new Request( '' );
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$requestParameterProvider must not be accessed before initialization' );
		$request->getRequestParameter( '' );
	}

	public function testSetRequestParameterReturnsRequest(): void
	{
		$this->assertInstanceOf( Request::class, $this->request->setRequestParameter( '', true ) );
	}

	public function testSetRequestParameterSetsQueryParameterIfQueryParameterProviderSet(): void
	{
		$key      = 'the question';
		$expected = 42;
		$this->requestParameterProvider->expects( $this->once() )
									   ->method( 'set' )
									   ->with( $key, $expected );
		$this->request->setRequestParameter( $key, $expected );
	}

	public function testSetRequestParameterThrowsIfQueryParameterProviderNotSet(): void
	{
		$request = new Request( '' );
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$requestParameterProvider must not be accessed before initialization' );
		$request->setRequestParameter( '', true );
	}

	public function testGetCookieReturnsCookieIfCookieProviderSet(): void
	{
		$name  = 'auth_token';
		$value = 'abc123';
		$this->cookieProvider->expects( $this->once() )
							 ->method( 'get' )
							 ->with( $name )
							 ->willReturn( $value );
		$result = $this->request->getCookie( $name );

		$this->assertNotNull( $result );
		$this->assertIsString( $result );
		$this->assertEquals( $value, $result );
	}

	public function testGetCookieThrowsIfCookieProviderNotSet(): void
	{
		$request = new Request( '' );
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$cookieProvider must not be accessed before initialization' );
		$request->getCookie( '' );
	}
}
