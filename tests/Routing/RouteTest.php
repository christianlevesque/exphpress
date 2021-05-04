<?php

namespace Routing;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
	private Route $route;

	protected function setUp(): void
	{
		$this->route = new Route( '/user/:id/email' );
	}

	public function testCanBeCreated()
	{
		$this->assertInstanceOf( Route::class, $this->route );
	}

	public function testGetHandlersReturnsHandlersArray()
	{
		$handlers = $this->route->getHandlers();
		$this->assertIsArray( $handlers );
		$this->assertCount( 0, $handlers );
	}

	public function testToStringReturnsRouteString(): void
	{
		$this->assertEquals( '/user/:id/email', (string) $this->route );
	}

	public function testAddHandlerAddsHandler(): void
	{
		$this->assertCount( 0, $this->route->getHandlers() );
		$this->route->addHandler( 'POST', function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testGetHandlerReturnsHandlerIfExists(): void
	{
		$this->route->addHandler( 'POST', function ()
		{
		} );
		$this->assertNotNull( $this->route->getHandler( 'POST' ) );
	}

	public function testGetHandlerReturnsNullIfNotExists(): void
	{
		$this->assertNull( $this->route->getHandler( 'POST' ) );
	}

	public function testAddHandlerThrowsInvalidArgumentExceptionIfInvalidHttpVerbPassed(): void
	{
		$this->expectErrorMessage( "'FAKE' is not a valid HTTP method." );
		$this->route->addHandler( 'FAKE', function ()
		{
		} );
		$this->assertCount( 0, $this->route->getHandlers() );
	}

	public function testAddHandlerReturnsRoute(): void
	{
		$this->assertInstanceOf( Route::class, $this->route->addHandler( 'GET', function ()
		{
		} ) );
	}

	public function testAnyCallsAddHandler(): void
	{
		$this->route->any( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testConnectCallsAddHandler(): void
	{
		$this->route->connect( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testDeleteCallsAddHandler(): void
	{
		$this->route->delete( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testGetCallsAddHandler(): void
	{
		$this->route->get( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testHeadCallsAddHandler(): void
	{
		$this->route->head( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testOptionsCallsAddHandler(): void
	{
		$this->route->options( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testPatchCallsAddHandler(): void
	{
		$this->route->patch( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testPostCallsAddHandler(): void
	{
		$this->route->post( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testPutCallsAddHandler(): void
	{
		$this->route->put( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testTraceCallsAddHandler(): void
	{
		$this->route->trace( function ()
		{
		} );
		$this->assertCount( 1, $this->route->getHandlers() );
	}

	public function testExecuteExecutesHandlerIfExists(): void
	{
		$response = $this->getMockBuilder( Response::class )
						 ->getMock();
		$response->expects( $this->once() )
				 ->method( 'status' )
				 ->with( 413 );

		$request = $this->createStub( Request::class );
		$request->method( 'getMethod' )
				->willReturn( 'POST' );

		$this->route->post( function ( Request $request, Response $response )
		{
			$response->status( 413 )
					 ->send();
		} );

		$this->route->execute( $request, $response );
	}

	public function testExecuteExecutesAllHandlerIfExistsAndRouteHandlerNotExists(): void
	{
		$response = $this->getMockBuilder( Response::class )
						 ->getMock();
		$response->expects( $this->once() )
				 ->method( 'status' )
				 ->with( 404 );

		$request = $this->createStub( Request::class );
		$request->method( 'getMethod' )
				->willReturn( 'POST' );

		$this->route->any( function ( Request $request, Response $response )
		{
			$response->status( 404 )
					 ->send();
		} );

		$this->route->execute( $request, $response );
	}

	public function testExecuteExecutesGeneratedAllowHandlerIfNoAppropriateHandlerFound(): void
	{
		$response = $this->getMockBuilder( Response::class )
						 ->getMock();
		$response->expects( $this->once() )
				 ->method( 'status' )
				 ->with( 405 );

		$request = $this->createStub( Request::class );
		$request->method( 'getMethod' )
				->willReturn( 'POST' );

		$this->route->delete( function ()
		{
		} );

		$this->route->execute( $request, $response );
	}
}
