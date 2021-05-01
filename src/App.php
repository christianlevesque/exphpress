<?php

namespace Crossview\Exphpress;

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
	 * @var string[] App-level status and error messages
	 */
	private array $messages;

	/**
	 * @var MiddlewareInterface[] Middleware container
	 */
	private array $middleware = [];

	private function __construct( string $domain )
	{
		$this->messages = require __DIR__ . '/config/strings.php';

		if ( !$domain && strcmp( $domain, '' ) !== 0 )
		{
			throw new InvalidArgumentException( sprintf( $this->messages[ 'required_arg' ], 'domain' ) );
		}

		$this->router   = Router::getInstance();
		$this->request  = new Request;
		$this->response = new Response( $domain );
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
	 * @return $this
	 */
	public function register( MiddlewareInterface $middleware ): App
	{
		array_push( $this->middleware, $middleware );

		return $this;
	}

	/**
	 * Executes the middleware registered on the application
	 */
	public function execute(): void
	{
		// We need Request and Response as local references for the closure
		$request  = $this->request;
		$response = $this->response;

		// The pipeline will just be a function chain
		// The last function in the pipeline will be empty so we don't have to do any null checking
		$pipeline = function ()
		{
		};

		// Wrap each new middleware around the existing pipeline
		// Start from the end of the array so we can have each next() call ready to go
		foreach ( array_reverse($this->middleware) as $currentMiddleware )
		{
			// The closure needs to reference the current middleware, the request, the response, and the existing pipeline
			// The existing pipeline will be used as next() for the new pipeline
			$pipeline = function () use ( $currentMiddleware, &$request, &$response, $pipeline )
			{
				$currentMiddleware->handle( $request, $response, $pipeline );
			};
		}

		// Execute the pipeline
		$pipeline();
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
	 * @return MiddlewareInterface[]
	 */
	public function getMiddleware(): array
	{
		return $this->middleware;
	}
}