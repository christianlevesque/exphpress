<?php

namespace Http;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Providers\ArrayValueProvider;
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

	public function testGetQueryParameterReturnsParameterIfQueryParameterProviderSet(): void
	{
		$this->request->setQueryParameterProvider( new ArrayValueProvider( [ 'id' => 42 ] ) );
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

	public function testGetRequestParameterReturnsParameterIfRequestParameterProviderSet(): void
	{
		$this->request->setRequestParameterProvider( new ArrayValueProvider( [ 'old_id' => 84 ] ) );
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

	public function testSetServerProviderOnlySetsServerProviderOnce(): void
	{
		$expected = 'GET';
		$this->request->setServerProvider( new ArrayValueProvider( [ 'REQUEST_METHOD' => $expected ] ) );
		$this->request->setServerProvider( new ArrayValueProvider( [ 'REQUEST_METHOD' => 'POST' ] ) );

		$result = $this->request->getMethod();

		$this->assertEquals( $expected, $result );
	}

	public function testSetQueryParameterProviderOnlySetsQueryParameterProviderOnce(): void
	{
		$expected = 'life, the universe, and everything';
		$this->request->setQueryParameterProvider( new ArrayValueProvider( [ 'search' => $expected ] ) );
		$this->request->setQueryParameterProvider( new ArrayValueProvider( [ 'search' => 'what is 42' ] ) );

		$result = $this->request->getQueryParameter( 'search' );

		$this->assertEquals( $expected, $result );
	}

	public function testSetRequestParameterProviderOnlySetsRequestParameterProviderOnce(): void
	{
		$expected = 42;
		$this->request->setRequestParameterProvider( new ArrayValueProvider( [ 'old_id' => $expected ] ) );
		$this->request->setRequestParameterProvider( new ArrayValueProvider( [ 'old_id' => 84 ] ) );

		$result = $this->request->getRequestParameter( 'old_id' );

		$this->assertEquals( $expected, $result );
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
