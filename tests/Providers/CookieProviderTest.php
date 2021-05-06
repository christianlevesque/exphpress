<?php

namespace Providers;

use Crossview\Exphpress\Providers\CookieProvider;
use PHPUnit\Framework\TestCase;

class CookieProviderTest extends TestCase
{
	private const COOKIE_NAME  = 'my_cookie';
	private const COOKIE_VALUE = '47 cookies';
	private CookieProvider $provider;

	public function setUp(): void
	{
		$this->provider = new CookieProvider( [], [] );
		$this->provider->setCookie( self::COOKIE_NAME, self::COOKIE_VALUE );
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( CookieProvider::class, $this->provider );
	}

	public function testConstructorCreatesExpectedDefaultCookieOptions(): void
	{
		$result = $this->provider->getCookieOptions();
		$this->assertArrayHasKey( 'expires', $result );
		$this->assertArrayHasKey( 'path', $result );
		$this->assertArrayHasKey( 'domain', $result );
		$this->assertArrayHasKey( 'secure', $result );
		$this->assertArrayHasKey( 'httponly', $result );
		$this->assertArrayHasKey( 'samesite', $result );
		$this->assertIsNumeric( $result[ 'expires' ] );
		$this->assertEquals( '/', $result[ 'path' ] );
		$this->assertEquals( '', $result[ 'domain' ] );
		$this->assertTrue( $result[ 'secure' ] );
		$this->assertTrue( $result[ 'httponly' ] );
		$this->assertEquals( 'lax', $result[ 'samesite' ] );
	}

	public function testConstructorMergesUserProvidedCookieOptionsWithDefaultCookieOptions(): void
	{
		$this->provider = new CookieProvider( [ 'samesite' => 'strict' ] );
		$result         = $this->provider->getCookieOptions();
		$this->assertEquals( 'strict', $result[ 'samesite' ] );
	}

	public function testGetCookieOptionsReturnsCookieOptions(): void
	{
		$result = $this->provider->getCookieOptions();
		$this->assertIsArray( $result );
		$this->assertCount( 6, $result );
		$this->assertArrayHasKey( 'path', $result );
		$this->assertEquals( '/', $result[ 'path' ] );
	}

	public function testGetCookieGetsCookie(): void
	{
		$result = $this->provider->getCookie( self::COOKIE_NAME );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'value', $result );
		$this->assertArrayHasKey( 'options', $result );
		$this->assertEquals( self::COOKIE_VALUE, $result[ 'value' ] );

		$options = $result[ 'options' ];
		$this->assertIsArray( $options );
		$this->assertCount( 6, $options );
	}

	public function testGetCookieReturnsNullIfCookieNotExists(): void
	{
		$result = $this->provider->getCookie( 'oatmeal raisin' );
		$this->assertNull( $result );
	}

	public function testSetCookieSetsCookie(): void
	{
		$name  = 'my_other_cookie';
		$value = 'also 47 cookies';

		$this->provider->setCookie( $name, $value );
		$result = $this->provider->getCookie( $name );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'value', $result );
		$this->assertArrayHasKey( 'options', $result );
		$this->assertEquals( $value, $result[ 'value' ] );

		$options = $result[ 'options' ];
		$this->assertIsArray( $options );
		$this->assertCount( 6, $options );
	}

	public function testSetCookieMergesUserOptionsWithDefaultOptions(): void
	{
		$name    = 'my_other_cookie';
		$value   = 'also 47 cookies';
		$options = [
			'domain' => 'mycookies.com'
		];

		$this->provider->setCookie( $name, $value, $options );
		$result = $this->provider->getCookie( $name );

		$this->assertEquals( $options[ 'domain' ], $result[ 'options' ][ 'domain' ] );
	}

	public function testSetCookieReturnsCookieProvider(): void
	{
		$result = $this->provider->setCookie( '', '' );

		$this->assertInstanceOf( CookieProvider::class, $result );
	}

	public function testUnsetCookieUnsetsCookie(): void
	{
		$this->provider->unsetCookie( self::COOKIE_NAME );
		$result = $this->provider->getCookie( self::COOKIE_NAME );

		$this->assertNull( $result );
	}

	public function testUnsetCookieReturnsCookieProvider(): void
	{
		$result = $this->provider->unsetCookie( self::COOKIE_NAME );
		$this->assertInstanceOf( CookieProvider::class, $result );
	}

	public function testDeleteCookieCreatesADeleteCookie(): void
	{
		$deletedName = 'stale cookies';
		$this->provider->deleteCookie( $deletedName );

		$result = $this->provider->getCookie( $deletedName );

		$this->assertIsArray( $result );

		$this->assertEmpty( $result[ 'value' ] );

		$options = $result[ 'options' ];
		$this->assertEquals( 1, $options[ 'expires' ] );
	}

	public function testDeleteCookieMergesUserOptionsWithDefaultOptions(): void
	{
		$deletedName = 'stale cookies';
		$this->provider->deleteCookie( $deletedName, [
			'expires' => 2,
			'path'    => '/home'
		] );

		$options = $this->provider->getCookie( $deletedName )[ 'options' ];

		$this->assertEquals( 2, $options[ 'expires' ] );
		$this->assertEquals( '/home', $options[ 'path' ] );
	}

	public function testDeleteCookieReturnsCookieProvider(): void
	{
		$result = $this->provider->deleteCookie( self::COOKIE_NAME );
		$this->assertInstanceOf( CookieProvider::class, $result );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendCookiesSendsCookies(): void
	{
		$this->provider->sendCookies();
		$headers = xdebug_get_headers();
		$this->assertCount( 1, $headers );
		$this->assertStringStartsWith( 'Set-Cookie: my_cookie', $headers[ 0 ] );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendCookiesDoesNothingIfNoCookiesQueued(): void
	{
		$this->provider->unsetCookie( self::COOKIE_NAME );
		$this->provider->sendCookies();
		$headers = xdebug_get_headers();
		$this->assertCount( 0, $headers );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendCookiesClearsCookieQueue(): void
	{
		$this->provider->sendCookies();
		$cookie = $this->provider->getCookie( self::COOKIE_NAME );
		$this->assertNull( $cookie );
	}
}
