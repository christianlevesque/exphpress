<?php

namespace Middleware;

use Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\MiddlewareContainer;
use Crossview\Exphpress\Middleware\MiddlewareInterface;
use PHPUnit\Framework\TestCase;

class MiddlewareContainerTest extends TestCase
{
	private MiddlewareContainer $container;

	protected function setUp(): void
	{
		$this->container = new MiddlewareContainer( function ()
		{
		} );
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

	public function testBuildPipelineReturnsMiddlewareContainer(): void
	{
		$request = $this->createStub(Request::class);
		$container = $this->container->buildPipeline( $request, new Response( '' ) );
		$this->assertInstanceOf( MiddlewareContainer::class, $container );
	}
}

class TestImpl implements MiddlewareInterface
{
	public function handle( Request $request, Response $response, Closure $next )
	{
		// Test implementation, should do nothing
	}
}