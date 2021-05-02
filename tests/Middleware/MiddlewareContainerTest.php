<?php

namespace Middleware;

use Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\MiddlewareContainer;
use Crossview\Exphpress\Middleware\MiddlewareInterface;
use Crossview\Exphpress\Providers\ArrayValueProvider;
use Crossview\Exphpress\Providers\WritableArrayValueProvider;
use PHPUnit\Framework\TestCase;

class MiddlewareContainerTest extends TestCase
{
	private MiddlewareContainer $container;
	private Request             $request;

	protected function setUp(): void
	{
		$this->request = new Request();
		$this->request->setQueryParameterProvider( new WritableArrayValueProvider( [] ) );
		$request         = $this->request;
		$this->container = new MiddlewareContainer( function () use ( &$request )
		{
			$request->setServerProvider( new ArrayValueProvider( [ 'REQUEST_METHOD' => 'GET' ] ) );
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
		$container = $this->container->buildPipeline( $this->request, new Response( '' ) );
		$this->assertInstanceOf( MiddlewareContainer::class, $container );
	}

	public function testBuildPipelineBuildsClosurePipeline(): void
	{
		$this->container->register( new TestImpl2() );
		$this->container->buildPipeline( $this->request, new Response( '' ) );
		$this->container->execute();

		$this->assertEquals( 42, $this->request->getQueryParameter( 'the question' ) );
	}

	public function testExecuteExecutesPipeline(): void
	{
		$this->container->execute();
		$this->assertEquals( 'GET', $this->request->getMethod() );
	}
}

class TestImpl implements MiddlewareInterface
{
	public function handle( Request $request, Response $response, Closure $next )
	{
		// Test implementation, should do nothing
	}
}

class TestImpl2 implements MiddlewareInterface
{
	public function handle( Request $request, Response $response, Closure $next )
	{
		$request->setQueryParameter( 'the question', 42 );
	}
}