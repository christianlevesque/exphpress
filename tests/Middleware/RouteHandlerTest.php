<?php

namespace Middleware;

use Closure;
use Crossview\Exphpress\App;
use Crossview\Exphpress\Http\Handlers;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\RouteHandlerMiddleware;
use Crossview\Exphpress\Routing\Route;
use Crossview\Exphpress\Routing\Router;
use Crossview\Exphpress\Utilities\RouteProcessor;
use PHPUnit\Framework\TestCase;

class RouteHandlerTest extends TestCase
{
	private App                    $app;
	private RouteProcessor         $routeProcessor;
	private Handlers               $handlers;
	private Router                 $router;
	private Request                $request;
	private Response               $response;
	private Route                  $route;
	private RouteHandlerMiddleware $middleware;
	private Closure                $next;

	protected function setUp(): void
	{
		// Instances
		$this->app            = $this->createMock( App::class );
		$this->routeProcessor = $this->createMock( RouteProcessor::class );
		$this->handlers       = $this->createMock( Handlers::class );
		$this->router         = $this->createMock( Router::class );
		$this->request        = $this->createMock( Request::class );
		$this->response       = $this->createMock( Response::class );
		$this->route          = $this->createMock( Route::class );
		$this->next           = function ()
		{
		};

		// Methods
		$this->app->method( 'getRouter' )
				  ->willReturn( $this->router );
		$this->app->method( 'getHandlers' )
				  ->willReturn( $this->handlers );

		// SUT
		$this->middleware = new RouteHandlerMiddleware( $this->app, $this->routeProcessor );
	}

	// constructor
	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( RouteHandlerMiddleware::class, $this->middleware );
	}

	public function testGetsInstancesFromApp(): void
	{
		$this->app->expects( $this->once() )
				  ->method( 'getRouter' );
		$this->app->expects( $this->once() )
				  ->method( 'getHandlers' );

		new RouteHandlerMiddleware( $this->app, $this->routeProcessor );
	}

	// handle
	public function testHandleExecutesRouteIfMatches(): void
	{
		// routes
		$localRoute = $this->createMock( Route::class );
		$localRoute->expects( $this->never() )
				   ->method( 'execute' );

		$this->route->expects( $this->once() )
					->method( 'execute' )
					->with( $this->request, $this->response );

		// router
		$this->router->expects( $this->once() )
					 ->method( 'getRoutes' )
					 ->willReturn( [
						 $localRoute,
						 $this->route
					 ] );

		// processor
		$this->routeProcessor->expects( $this->exactly( 2 ) )
							 ->method( 'routeMatches' )
							 ->with( $this->route, $this->request )
							 ->willReturnOnConsecutiveCalls( false, true );

		// handlers
		$this->handlers->expects( $this->never() )
					   ->method( 'notFound' );

		$this->middleware->handle( $this->request, $this->response, $this->next );
	}

	public function testHandleCallsNotFoundIfNoRouteMatches(): void
	{
		$this->router->expects( $this->once() )
					 ->method( 'getRoutes' )
					 ->willReturn( [] );

		$this->handlers->expects( $this->once() )
					   ->method( 'notFound' )
					   ->with( $this->request, $this->response );

		$this->middleware->handle( $this->request, $this->response, $this->next );
	}
}