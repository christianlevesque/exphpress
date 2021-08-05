<?php

namespace Middleware;

use Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\Middleware;
use Crossview\Exphpress\Middleware\MiddlewareContainer;
use PHPUnit\Framework\TestCase;

class MiddlewareContainerTest extends TestCase
{
	private MiddlewareContainer $container;
	private Request             $request;
	private Response            $response;

	protected function setUp(): void
	{
		$this->request   = $this->createStub( Request::class );
		$this->response  = $this->createStub( Response::class );
		$this->container = new MiddlewareContainer( $this->request, $this->response );
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( MiddlewareContainer::class, $this->container );
	}

	public function testMiddlewareIsArray(): void
	{
		$middleware = $this->container->getMiddleware();
		$this->assertIsArray( $middleware );
		$this->assertCount( 0, $middleware );
	}

	public function testRegisterAddsMiddlewareToContainer(): void
	{
		$this->container->register( new TestImpl() );
		$this->assertCount( 1, $this->container->getMiddleware() );
	}

	public function testRegisterReturnsMiddlewareContainer(): void
	{
		$container = $this->container->register( new TestImpl() );
		$this->assertInstanceOf( MiddlewareContainer::class, $container );
	}

	public function testGetMiddlewareReturnsMiddlewareArray(): void
	{
		$middleware = $this->container->getMiddleware();
		$this->assertIsArray( $middleware );
		$this->assertCount( 0, $middleware );
		$this->container->register( new TestImpl() );
		$this->assertCount( 1, $this->container->getMiddleware() );
	}

	public function testExecuteExecutesPipeline(): void
	{
		$this->response->expects( $this->once() )
						->method( 'send' )
						->with( "I'm done!" );

		$container = new MiddlewareContainer( $this->request, $this->response );
		$container->register( new TestImpl() );
		$container->execute();
	}

	public function testExecuteDoesNothingIfNoPipeline(): void
	{
		$this->response->expects( $this->never() )
					   ->method( 'send' )
					   ->withAnyParameters();

		$container = new MiddlewareContainer( $this->request, $this->response );
		$container->execute();
	}
}

class TestImpl implements Middleware
{
	public function handle( Request $request, Response $response, Closure $next )
	{
		$response->send( "I'm done!" );
	}
}