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
		$request = $this->request;
		$response = $this->response;
		$pipeline = function() {};
		for ( $i = count( $this->middleware ) - 1; $i >= 0; $i-- )
		{
			$currentMiddleware = $this->middleware[$i];
			$closure = function() use ($currentMiddleware, &$request, &$response, $pipeline) {
				$currentMiddleware->handle( $request, $response, $pipeline );
			};
			$pipeline = $closure;
		}
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