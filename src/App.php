<?php

namespace Crossview\Exphpress;

use Crossview\Exphpress\Middleware\MiddlewareContainer;
use \InvalidArgumentException;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\MiddlewareInterface;

class App
{
	private static App $instance;
	private Router     $router;
	private Request    $request;
	private Response   $response;

	/**
	 * @var MiddlewareContainer Middleware container
	 */
	private MiddlewareContainer $middleware;

	private function __construct( string $domain )
	{

		$this->router     = Router::getInstance();
		$this->request    = new Request;
		$this->response   = new Response( $domain );
		$this->middleware = new MiddlewareContainer();
	}

	public static function getInstance( string $domain ): App
	{
		if ( !isset( self::$instance ) )
		{
			self::$instance = new self( $domain ?? '' );
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