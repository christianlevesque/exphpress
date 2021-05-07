<?php

namespace Middleware;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\RequestConfigurer;
use Crossview\Exphpress\Providers\ArrayValueProvider;
use Crossview\Exphpress\Providers\WritableArrayValueProvider;
use PHPUnit\Framework\TestCase;

class RequestConfigurerTest extends TestCase
{
	private RequestConfigurer $middleware;
	private Request           $request;
	private Response          $response;

	protected function setUp(): void
	{
		$this->middleware = new RequestConfigurer();
		$this->request    = $this->createMock( Request::class );
		$this->response   = $this->createStub( Response::class );

		$this->request->method( 'getOriginalUrl' )
					  ->willReturn( '/' );
		$this->request->method( 'getServerParameter' )
					  ->with( 'REQUEST_URI' )
					  ->willReturn( '/' );
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( RequestConfigurer::class, $this->middleware );
	}

	public function testCallsConfigureProviders(): void
	{
		$this->request->expects( $this->once() )
					  ->method( 'setServerProvider' )
					  ->with( $this->isInstanceOf( ArrayValueProvider::class ) );
		$this->request->expects( $this->once() )
					  ->method( 'setCookieProvider' )
					  ->with( $this->isInstanceOf( ArrayValueProvider::class ) );
		$this->request->expects( $this->once() )
					  ->method( 'setQueryParameterProvider' )
					  ->with( $this->isInstanceOf( WritableArrayValueProvider::class ) );
		$this->request->expects( $this->once() )
					  ->method( 'setRequestParameterProvider' )
					  ->with( $this->isInstanceOf( WritableArrayValueProvider::class ) );

		$this->middleware->handle( $this->request, $this->response, function ()
		{
		} );
	}

	public function testCallsConfigureUrl(): void
	{
		$this->request->expects( $this->once() )
					  ->method( 'setOriginalUrl' )
					  ->with( '/' );
		$this->request->expects( $this->once() )
					  ->method( 'setPath' )
					  ->with( '/' );

		$this->middleware->handle( $this->request, $this->response, function ()
		{
		} );
	}

	public function testCallsNextMiddleware(): void
	{
		$output = 'Some output should probably go here';

		ob_start();
		$this->middleware->handle( $this->request, $this->response, function () use ( $output )
		{
			echo $output;
		} );
		$this->assertEquals( $output, ob_get_clean() );
	}
}