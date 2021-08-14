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
		$this->middleware = new RequestConfigurer( '{"someKey":"This is a key"}' );
		$this->request    = $this->createMock( Request::class );
		$this->response   = $this->createStub( Response::class );

		$this->request->method( 'getOriginalUrl' )
					  ->willReturn( '/' );
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( RequestConfigurer::class, $this->middleware );
	}

	public function testGetInputReturnsInput()
	{
		$this->assertEquals( '{"someKey":"This is a key"}', $this->middleware->getInput() );
	}

	public function testSetInputSetsInput()
	{
		$this->middleware->setInput( "other input" );
		$this->assertEquals( "other input", $this->middleware->getInput() );
	}

	public function testCallsConfigureProviders(): void
	{
		$this->request->method( 'getServerParameter' )
					  ->will( $this->returnValueMap( [
						  [
							  'REQUEST_URI',
							  '/'
						  ],
						  [
							  'CONTENT_TYPE',
							  'application/json'
						  ]
					  ] ) );
		$this->request->expects( $this->once() )
					  ->method( 'setServerProvider' )
					  ->with( $this->isInstanceOf( ArrayValueProvider::class ) );
		$this->request->expects( $this->once() )
					  ->method( 'setFileProvider' )
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
		$this->request->method( 'getServerParameter' )
					  ->will( $this->returnValueMap( [
						  [
							  'REQUEST_URI',
							  '/'
						  ],
						  [
							  'CONTENT_TYPE',
							  'application/json'
						  ]
					  ] ) );
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
		$this->request->method( 'getServerParameter' )
					  ->will( $this->returnValueMap( [
						  [
							  'REQUEST_URI',
							  '/'
						  ],
						  [
							  'CONTENT_TYPE',
							  'application/json'
						  ]
					  ] ) );
		ob_start();
		$this->middleware->handle( $this->request, $this->response, function () use ( $output )
		{
			echo $output;
		} );
		$this->assertEquals( $output, ob_get_clean() );
	}

	// processAsJson
	public function testAddsJson(): void
	{
		$this->request->method( 'getServerParameter' )
					  ->will( $this->returnValueMap( [
						  [
							  'REQUEST_URI',
							  '/'
						  ],
						  [
							  'CONTENT_TYPE',
							  'application/json'
						  ]
					  ] ) );
		$this->request->expects( $this->once() )
					  ->method( 'setRequestParameterProvider' )
					  ->with( $this->callback( function ( $arg )
					  {
						  return $arg instanceof WritableArrayValueProvider
							  && $arg->get( 'someKey' ) === 'This is a key';
					  } )
					  );

		$this->middleware->handle( $this->request, $this->response, function ()
		{
		} );
	}

	public function testAddsFormData(): void
	{
		$this->request->method( 'getServerParameter' )
					  ->will( $this->returnValueMap( [
						  [
							  'REQUEST_URI',
							  '/'
						  ],
						  [
							  'CONTENT_TYPE',
							  'application/x-www-form-urlencoded'
						  ]
					  ] ) );

		$middleware = new RequestConfigurer( 'someVar=someValue&someOtherVar=someOtherValue' );

		$this->request->expects( $this->once() )
					  ->method( 'setRequestParameterProvider' )
					  ->with( $this->callback( function ( $arg )
					  {
						  return $arg instanceof WritableArrayValueProvider
							  && $arg->get( 'someVar' ) === 'someValue'
							  && $arg->get( 'someOtherVar' ) === 'someOtherValue';
					  } ) );

		$middleware->handle( $this->request, $this->response, function ()
		{
		} );
	}
}
