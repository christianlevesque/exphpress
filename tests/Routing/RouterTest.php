<?php

namespace Routing;

use Crossview\Exphpress\Routing\Route;
use Crossview\Exphpress\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
	private Router $router;

	protected function setUp(): void
	{
		$this->router = Router::getInstance();
		$this->router->route( '/default/route' );
	}

	protected function tearDown(): void
	{
		// Since Router is singleton, tearing down $this->router isn't enough because Router::getInstance() returns the same instance
		// This will also prevent Router from getting garbage collected (because it always has a reference to itself), but this shouldn't make much of a difference for the test suite
		Router::deleteInstance();
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( Router::class, $this->router );
	}

	public function testGetInstanceReturnsInstanceIfExists(): void
	{
		$router = Router::getInstance();
		$this->assertInstanceOf( Router::class, $router );

		$route = $router->route( '/default/route' );
		$this->assertNotNull( $route );
		$this->assertInstanceOf( Route::class, $route );
	}

	public function testDeleteInstanceDeletesInstance(): void
	{
		Router::deleteInstance();
		$this->assertFalse( Router::hasInstance() );
	}

	public function testRouteReturnsNewRouteIfNotExists(): void
	{
		$this->assertCount( 1, $this->router->getRoutes() );
		$route = $this->router->route( '/some/uri' );
		$this->assertInstanceOf( Route::class, $route );
		$this->assertCount( 2, $this->router->getRoutes() );
	}

	public function testRouteReturnsExistingRouteIfExists(): void
	{
		$this->assertCount( 1, $this->router->getRoutes() );
		$route = $this->router->route( '/default/route' );
		$this->assertInstanceOf( Route::class, $route );
		$this->assertCount( 1, $this->router->getRoutes() );
	}
}
