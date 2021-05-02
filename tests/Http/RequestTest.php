<?php

namespace Http;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Providers\ArrayValueProvider;
use Crossview\Exphpress\Providers\WritableArrayValueProvider;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
	private Request $request;

	protected function setUp(): void
	{
		$this->request = new Request();
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( Request::class, $this->request );
	}

	public function testGetMethodReturnsRequestMethodIfServerProviderSet(): void
	{
		$this->request->setServerProvider( new ArrayValueProvider( [ 'REQUEST_METHOD' => 'POST' ] ) );
		$result = $this->request->getMethod();

		$this->assertNotNull( $result );
		$this->assertIsString( $result );
		$this->assertEquals( 'POST', $result );
	}

	public function testGetMethodThrowsIfServerProviderNotSet(): void
	{
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$serverProvider must not be accessed before initialization' );
		$this->request->getMethod();
	}

	public function testGetServerParameterReturnsServerParameterIfServerProviderSet(): void
	{
		$this->request->setServerProvider( new ArrayValueProvider( [ 'some_value' => 42 ] ) );
		$result = $this->request->getServerParameter( 'some_value' );

		$this->assertIsNumeric( $result );
		$this->assertEquals( 42, $result );
	}

	public function testGetServerParameterThrowsIfServerProviderNotSet(): void
	{
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$serverProvider must not be accessed before initialization' );
		$this->request->getServerParameter( '' );
	}

	public function testGetQueryParameterReturnsParameterIfQueryParameterProviderSet(): void
	{
		$this->request->setQueryParameterProvider( new WritableArrayValueProvider( [ 'id' => 42 ] ) );
		$result = $this->request->getQueryParameter( 'id' );

		$this->assertNotNull( $result );
		$this->assertIsNumeric( $result );
		$this->assertEquals( 42, $result );
	}

	public function testGetQueryParameterThrowsIfQueryParameterProviderNotSet(): void
	{
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$queryParameterProvider must not be accessed before initialization' );
		$this->request->getQueryParameter( '' );
	}

	public function testSetQueryParameterReturnsRequest(): void
	{
		$result = $this->request->setQueryParameterProvider( new WritableArrayValueProvider( [] ) )
								->setQueryParameter( '', true );
		$this->assertInstanceOf( Request::class, $result );
	}

	public function testSetQueryParameterSetsQueryParameterIfQueryParameterProviderSet(): void
	{
		$key      = 'the question';
		$expected = 42;
		$this->request->setQueryParameterProvider( new WritableArrayValueProvider( [] ) )
					  ->setQueryParameter( $key, $expected );

		$value = $this->request->getQueryParameter( $key );

		$this->assertIsNumeric( $value );
		$this->assertEquals( $expected, $value );
	}

	public function testSetQueryParameterThrowsIfQueryParameterProviderNotSet(): void
	{
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$queryParameterProvider must not be accessed before initialization' );
		$this->request->setQueryParameter( '', true );
	}

	public function testGetRequestParameterReturnsParameterIfRequestParameterProviderSet(): void
	{
		$this->request->setRequestParameterProvider( new WritableArrayValueProvider( [ 'old_id' => 84 ] ) );
		$result = $this->request->getRequestParameter( 'old_id' );

		$this->assertNotNull( $result );
		$this->assertIsNumeric( $result );
		$this->assertEquals( 84, $result );
	}

	public function testGetRequestParameterThrowsIfRequestParameterProviderNotSet(): void
	{
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$requestParameterProvider must not be accessed before initialization' );
		$this->request->getRequestParameter( '' );
	}

	public function testSetRequestParameterReturnsRequest(): void
	{
		$result = $this->request->setRequestParameterProvider( new WritableArrayValueProvider( [] ) )
								->setRequestParameter( '', true );
		$this->assertInstanceOf( Request::class, $result );
	}

	public function testSetRequestParameterSetsQueryParameterIfQueryParameterProviderSet(): void
	{
		$key      = 'the question';
		$expected = 42;
		$this->request->setRequestParameterProvider( new WritableArrayValueProvider( [] ) )
					  ->setRequestParameter( $key, $expected );

		$value = $this->request->getRequestParameter( $key );

		$this->assertIsNumeric( $value );
		$this->assertEquals( $expected, $value );
	}

	public function testSetRequestParameterThrowsIfQueryParameterProviderNotSet(): void
	{
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$requestParameterProvider must not be accessed before initialization' );
		$this->request->setRequestParameter( '', true );
	}

	public function testGetCookieReturnsCookieIfCookieProviderSet(): void
	{
		$this->request->setCookieProvider( new ArrayValueProvider( [ 'auth_token' => 'abc123' ] ) );
		$result = $this->request->getCookie( 'auth_token' );

		$this->assertNotNull( $result );
		$this->assertIsString( $result );
		$this->assertEquals( 'abc123', $result );
	}

	public function testGetCookieThrowsIfCookieProviderNotSet(): void
	{
		$this->expectErrorMessage( 'Typed property Crossview\Exphpress\Http\Request::$cookieProvider must not be accessed before initialization' );
		$this->request->getCookie( '' );
	}

	public function testGetServerProviderReturnsServerProviderIfExists(): void
	{
		$this->request->setServerProvider( new ArrayValueProvider( [] ) );
		$result = $this->request->getServerProvider();

		$this->assertInstanceOf( ArrayValueProvider::class, $result );
	}

	public function testGetServerProviderReturnsNullIfNotExists(): void
	{
		$result = $this->request->getServerProvider();
		$this->assertNull( $result );
	}

	public function testSetServerProviderOnlySetsServerProviderOnce(): void
	{
		$expected = 'GET';
		$this->request->setServerProvider( new ArrayValueProvider( [ 'REQUEST_METHOD' => $expected ] ) );
		$this->request->setServerProvider( new ArrayValueProvider( [ 'REQUEST_METHOD' => 'POST' ] ) );

		$result = $this->request->getMethod();

		$this->assertEquals( $expected, $result );
	}

	public function testGetQueryParameterProviderReturnsQueryParameterProviderIfExists(): void
	{
		$this->request->setQueryParameterProvider( new WritableArrayValueProvider( [] ) );
		$result = $this->request->getQueryParameterProvider();

		$this->assertInstanceOf( WritableArrayValueProvider::class, $result );
	}

	public function testGetQueryParameterProviderReturnsNullIfNotExists(): void
	{
		$result = $this->request->getQueryParameterProvider();
		$this->assertNull( $result );
	}

	public function testSetQueryParameterProviderOnlySetsQueryParameterProviderOnce(): void
	{
		$expected = 'life, the universe, and everything';
		$this->request->setQueryParameterProvider( new WritableArrayValueProvider( [ 'search' => $expected ] ) );
		$this->request->setQueryParameterProvider( new WritableArrayValueProvider( [ 'search' => 'what is 42' ] ) );

		$result = $this->request->getQueryParameter( 'search' );

		$this->assertEquals( $expected, $result );
	}

	public function testGetRequestParameterProviderReturnsRequestParameterProviderIfExists(): void
	{
		$this->request->setRequestParameterProvider( new WritableArrayValueProvider( [] ) );
		$result = $this->request->getRequestParameterProvider();

		$this->assertInstanceOf( WritableArrayValueProvider::class, $result );
	}

	public function testGetRequestParameterProviderReturnsNullIfNotExists(): void
	{
		$result = $this->request->getRequestParameterProvider();
		$this->assertNull( $result );
	}

	public function testSetRequestParameterProviderOnlySetsRequestParameterProviderOnce(): void
	{
		$expected = 42;
		$this->request->setRequestParameterProvider( new WritableArrayValueProvider( [ 'old_id' => $expected ] ) );
		$this->request->setRequestParameterProvider( new WritableArrayValueProvider( [ 'old_id' => 84 ] ) );

		$result = $this->request->getRequestParameter( 'old_id' );

		$this->assertEquals( $expected, $result );
	}

	public function testGetCookieProviderReturnsCookieProviderIfExists(): void
	{
		$this->request->setCookieProvider( new ArrayValueProvider( [] ) );
		$result = $this->request->getCookieProvider();

		$this->assertInstanceOf( ArrayValueProvider::class, $result );
	}

	public function testGetCookieProviderReturnsNullIfNotExists(): void
	{
		$result = $this->request->getCookieProvider();
		$this->assertNull( $result );
	}

	public function testSetCookieProviderOnlySetsCookieProviderOnce(): void
	{
		$expected = 'abc123';
		$this->request->setCookieProvider( new ArrayValueProvider( [ 'auth_token' => $expected ] ) );
		$this->request->setCookieProvider( new ArrayValueProvider( [ 'auth_token' => 'xyz789' ] ) );

		$result = $this->request->getCookie( 'auth_token' );

		$this->assertEquals( $expected, $result );
	}
}
