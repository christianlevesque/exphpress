<?php

use PHPUnit\Framework\TestCase;
use Crossview\Exphpress\App;
use Crossview\Exphpress\Middleware\MiddlewareContainer;
use Crossview\Exphpress\Middleware\Middleware;
use Crossview\Exphpress\Http\Handlers;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Routing\Router;

class AppTest extends TestCase
{
	private App                 $app;
	private Router              $router;
	private Request             $request;
	private Response            $response;
	private MiddlewareContainer $middleware;
	private Middleware          $outputBuffer;
	private Middleware          $errorHandler;
	private Middleware          $requestConfigurer;
	private Middleware          $responseConfigurer;
	private Middleware          $routeHandler;
	private Handlers            $handlers;

	protected function setUp(): void
	{
		$this->app                = App::getInstance();
		$this->router             = $this->createMock( Router::class );
		$this->request            = $this->createMock( Request::class );
		$this->response           = $this->createMock( Response::class );
		$this->middleware         = $this->createMock( MiddlewareContainer::class );
		$this->outputBuffer       = $this->createMock( Middleware::class );
		$this->errorHandler       = $this->createMock( Middleware::class );
		$this->requestConfigurer  = $this->createMock( Middleware::class );
		$this->responseConfigurer = $this->createMock( Middleware::class );
		$this->routeHandler       = $this->createMock( Middleware::class );
		$this->handlers           = $this->createMock( Handlers::class );
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( App::class, $this->app );
	}

	// getInstance
	public function testGetInstanceReturnsSameInstance(): void
	{
		$this->assertSame( App::getInstance(), $this->app );
	}

	// getMiddlewareContainer
	public function testGetMiddlewareContainerReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getMiddlewareContainer() );
	}

	// setMiddlewareContainer
	public function testSetMiddlewareContainerSetsMiddlewareContainer(): void
	{
		$this->app->setMiddlewareContainer( $this->middleware );
		$this->assertSame( $this->middleware, $this->app->getMiddlewareContainer() );
	}

	public function testSetMiddlewareContainerReturnsApp(): void
	{
		$result = $this->app->setMiddlewareContainer( $this->middleware );
		$this->assertInstanceOf( App::class, $result );
	}

	// getRequest
	public function testGetRequestReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getRequest() );
	}

	// setRequest
	public function testSetRequestSetsRequest(): void
	{
		$this->app->setRequest( $this->request );
		$this->assertSame( $this->request, $this->app->getRequest() );
	}

	public function testSetRequestReturnsApp(): void
	{
		$result = $this->app->setRequest( $this->request );
		$this->assertInstanceOf( App::class, $result );
	}

	// getResponse
	public function testGetResponseReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getResponse() );
	}

	// setResponse
	public function testSetResponseSetsResponse(): void
	{
		$this->app->setResponse( $this->response );
		$this->assertSame( $this->response, $this->app->getResponse() );
	}

	public function testSetResponseReturnsApp(): void
	{
		$result = $this->app->setResponse( $this->response );
		$this->assertInstanceOf( App::class, $result );
	}

	// getRouter
	public function testGetRouterReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getRouter() );
	}

	// setRouter
	public function testSetRouterSetsRouter(): void
	{
		$this->app->setRouter( $this->router );
		$this->assertSame( $this->router, $this->app->getRouter() );
	}

	public function testSetRouterReturnsApp(): void
	{
		$result = $this->app->setRouter( $this->router );
		$this->assertInstanceOf( App::class, $result );
	}

	// getOutputBuffer
	public function testGetOutputBufferReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getOutputBuffer() );
	}

	// setOutputBuffer
	public function testSetOutputBufferSetsOutputBuffer(): void
	{
		$this->app->setOutputBuffer( $this->outputBuffer );
		$this->assertSame( $this->outputBuffer, $this->app->getOutputBuffer() );
	}

	public function testSetOutputBufferReturnsApp(): void
	{
		$result = $this->app->setOutputBuffer( $this->outputBuffer );
		$this->assertInstanceOf( App::class, $result );
	}

	// getErrorHandler
	public function testGetErrorHandlerReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getErrorHandler() );
	}

	// setErrorHandler
	public function testSetErrorHandlerSetsErrorHandler(): void
	{
		$this->app->setErrorHandler( $this->errorHandler );
		$this->assertSame( $this->errorHandler, $this->app->getErrorHandler() );
	}

	public function testSetErrorHandlerReturnsApp(): void
	{
		$result = $this->app->setErrorHandler( $this->errorHandler );
		$this->assertInstanceOf( App::class, $result );
	}

	// getRequestConfigurer
	public function testGetRequestConfigurerReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getRequestConfigurer() );
	}

	// setRequestConfigurer
	public function testSetRequestConfigurerSetsRequestConfigurer(): void
	{
		$this->app->setRequestConfigurer( $this->requestConfigurer );
		$this->assertSame( $this->requestConfigurer, $this->app->getRequestConfigurer() );
	}

	public function testSetRequestConfigurerReturnsApp(): void
	{
		$result = $this->app->setRequestConfigurer( $this->requestConfigurer );
		$this->assertInstanceOf( App::class, $result );
	}

	// getResponseConfigurer
	public function testGetResponseConfigurerReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getResponseConfigurer() );
	}

	// setResponseConfigurer
	public function testSetResponseConfigurerSetsResponseConfigurer(): void
	{
		$this->app->setResponseConfigurer( $this->responseConfigurer );
		$this->assertSame( $this->responseConfigurer, $this->app->getResponseConfigurer() );
	}

	public function testSetResponseConfigurerReturnsApp(): void
	{
		$result = $this->app->setResponseConfigurer( $this->responseConfigurer );
		$this->assertInstanceOf( App::class, $result );
	}

	// getRouteHandler
	public function testGetRouteHandlerReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getRouteHandler() );
	}

	// setRouteHandler
	public function testSetRouteHandlerSetsRouteHandler(): void
	{
		$this->app->setRouteHandler( $this->routeHandler );
		$this->assertSame( $this->routeHandler, $this->app->getRouteHandler() );
	}

	public function testSetRouteHandlerReturnsApp(): void
	{
		$result = $this->app->setRouteHandler( $this->routeHandler );
		$this->assertInstanceOf( App::class, $result );
	}

	// getRouteHandler
	public function testGetHandlersReturnsNullIfNotSet(): void
	{
		$this->assertNull( $this->app->getHandlers() );
	}

	// setRouteHandler
	public function testSetHandlersSetsHandlers(): void
	{
		$this->app->setHandlers( $this->handlers );
		$this->assertSame( $this->handlers, $this->app->getHandlers() );
	}

	public function testSetHandlersReturnsApp(): void
	{
		$result = $this->app->setHandlers( $this->handlers );
		$this->assertInstanceOf( App::class, $result );
	}

	// configure
	public function testConfigureUsesProvidedInstances(): void
	{
		$this->app->setRouter( $this->router )
				  ->setRequest( $this->request )
				  ->setResponse( $this->response )
				  ->setMiddlewareContainer( $this->middleware )
				  ->setOutputBuffer( $this->outputBuffer )
				  ->setErrorHandler( $this->errorHandler )
				  ->setRequestConfigurer( $this->requestConfigurer )
				  ->setResponseConfigurer( $this->responseConfigurer )
				  ->setRouteHandler( $this->routeHandler )
				  ->setHandlers( $this->handlers );

		$this->app->configure();

		$this->assertSame( $this->router, $this->app->getRouter() );
		$this->assertSame( $this->request, $this->app->getRequest() );
		$this->assertSame( $this->response, $this->app->getResponse() );
		$this->assertSame( $this->middleware, $this->app->getMiddlewareContainer() );
		$this->assertSame( $this->outputBuffer, $this->app->getOutputBuffer() );
		$this->assertSame( $this->errorHandler, $this->app->getErrorHandler() );
		$this->assertSame( $this->requestConfigurer, $this->app->getRequestConfigurer() );
		$this->assertSame( $this->responseConfigurer, $this->app->getResponseConfigurer() );
		$this->assertSame( $this->routeHandler, $this->app->getRouteHandler() );
		$this->assertSame( $this->handlers, $this->app->getHandlers() );
	}

	public function testConfigureSetsDefaultInstances(): void
	{
		$this->app->configure();

		$this->assertNotNull( $this->app->getRouter() );
		$this->assertNotNull( $this->app->getRequest() );
		$this->assertNotNull( $this->app->getResponse() );
		$this->assertNotNull( $this->app->getMiddlewareContainer() );
		$this->assertNotNull( $this->app->getOutputBuffer() );
		$this->assertNotNull( $this->app->getErrorHandler() );
		$this->assertNotNull( $this->app->getRequestConfigurer() );
		$this->assertNotNull( $this->app->getResponseConfigurer() );
		$this->assertNotNull( $this->app->getRouteHandler() );
		$this->assertNotNull( $this->app->getHandlers() );

		$this->assertNotSame( $this->router, $this->app->getRouter() );
		$this->assertNotSame( $this->request, $this->app->getRequest() );
		$this->assertNotSame( $this->response, $this->app->getResponse() );
		$this->assertNotSame( $this->middleware, $this->app->getMiddlewareContainer() );
		$this->assertNotSame( $this->outputBuffer, $this->app->getOutputBuffer() );
		$this->assertNotSame( $this->errorHandler, $this->app->getErrorHandler() );
		$this->assertNotSame( $this->requestConfigurer, $this->app->getRequestConfigurer() );
		$this->assertNotSame( $this->responseConfigurer, $this->app->getResponseConfigurer() );
		$this->assertNotSame( $this->routeHandler, $this->app->getRouteHandler() );
		$this->assertNotSame( $this->handlers, $this->app->getHandlers() );
	}

	public function testConfigureRegistersMiddleware(): void
	{
		$this->middleware->expects( $this->exactly( 4 ) )
						 ->method( 'register' )
						 ->withConsecutive(
							 [ $this->outputBuffer ],
							 [ $this->errorHandler ],
							 [ $this->requestConfigurer ],
							 [ $this->responseConfigurer ]
						 );

		$this->app->setOutputBuffer( $this->outputBuffer )
				  ->setErrorHandler( $this->errorHandler )
				  ->setRequestConfigurer( $this->requestConfigurer )
				  ->setResponseConfigurer( $this->responseConfigurer )
				  ->setRouteHandler( $this->routeHandler )
				  ->setMiddlewareContainer( $this->middleware )
				  ->configure();
	}

	public function testConfigureReturnsApp(): void
	{
		$result = $this->app->configure();
		$this->assertInstanceOf( App::class, $result );
	}

	// register
	public function testRegisterCallsMiddlewareContainerRegister(): void
	{
		$this->middleware->expects( $this->once() )
						 ->method( 'register' )
						 ->with( $this->outputBuffer );

		$this->app->setMiddlewareContainer( $this->middleware )
				  ->register( $this->outputBuffer );
	}

	public function testRegisterReturnsApp(): void
	{
		$result = $this->app->register( $this->outputBuffer );
		$this->assertInstanceOf( App::class, $result );
	}

	// execute
	public function testExecuteRegistersRouteHandlerMiddleware(): void
	{
		$this->app->setMiddlewareContainer( $this->middleware )
				  ->setRouteHandler( $this->routeHandler )
				  ->configure();

		$this->middleware->expects( $this->once() )
						 ->method( 'register' )
						 ->with( $this->routeHandler );

		$this->app->execute();
	}

	public function testExecuteCallsMiddlewareContainerExecute(): void
	{
		$this->app->setMiddlewareContainer( $this->middleware )
				  ->configure();

		$this->middleware->expects( $this->once() )
						 ->method( 'execute' );

		$this->app->execute();
	}
}