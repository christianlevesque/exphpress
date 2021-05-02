<?php

namespace Crossview\Exphpress;

use Crossview\Exphpress\Middleware\MiddlewareContainer;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\MiddlewareInterface;

class App
{
	protected static App $instance;
	protected Router     $router;
	protected Request    $request;
	protected Response   $response;

	/**
	 * @var MiddlewareContainer Middleware container
	 */
	protected MiddlewareContainer $middleware;

	protected function __construct()
	{

		$this->router     = Router::getInstance();
		$this->request    = new Request;
		$this->response   = new Response();
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
		$this->middleware->buildPipeline( $this->request, $this->response )
						 ->execute();
	}

	/**
	 * @return Router
	 */
	public function getRouter(): Router
	{
		return $this->router;
	}

	/**
	 * @return Request
	 */
	public function getRequest(): Request
	{
		return $this->request;
	}

	/**
	 * @return Response
	 */
	public function getResponse(): Response
	{
		return $this->response;
	}

	/**
	 * @return MiddlewareContainer
	 */
	public function getMiddleware(): MiddlewareContainer
	{
		return $this->middleware;
	}
}