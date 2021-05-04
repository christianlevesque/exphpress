<?php

namespace Crossview\Exphpress;

use Crossview\Exphpress\Middleware\MiddlewareContainer;
use Crossview\Exphpress\Middleware\MiddlewareInterface;
use Crossview\Exphpress\Routing\Router;

class App
{
	protected static App $instance;
	protected Router     $router;

	/**
	 * @var MiddlewareContainer Middleware container
	 */
	protected MiddlewareContainer $middleware;

	protected function __construct()
	{
		$this->router     = Router::getInstance();
		$this->middleware = new MiddlewareContainer(function() {});
	}

	public static function getInstance(): App
	{
		if ( !isset( self::$instance ) )
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers a middleware on the application
	 *
	 * @param MiddlewareInterface $middleware The middleware to register
	 *
	 * @return $this
	 */
	public function register( MiddlewareInterface $middleware ): App
	{
		$this->middleware->register( $middleware );

		return $this;
	}

	/**
	 * Executes the middleware registered on the application
	 */
	public function execute(): void
	{
		$this->middleware->execute();
	}

	/**
	 * @return Router
	 */
	public function getRouter(): Router
	{
		return $this->router;
	}

	/**
	 * @return MiddlewareContainer
	 */
	public function getMiddleware(): MiddlewareContainer
	{
		return $this->middleware;
	}
}