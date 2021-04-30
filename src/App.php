<?php

namespace Crossview\Exphpress;

use \Closure;
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

	/**
	 * @var Closure[] Next Middleware container
	 */
	private array $nextMiddlewares = [];

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
		$request  = $this->request;
		$response = $this->response;

		for ( $i = 0; $i < count( $this->middleware ); $i++ )
		{
			if ( array_key_exists( $i + 1, $this->middleware ) )
			{
				$next = function () use ( $i, &$request, &$response )
				{
					// TODO: figure out how to construct each next() (probably will have to traverse the middleware container backwards, construct each next and unpush it on an array, then traverse the array normally when calling the middleware
					$this->middleware[ $i + 1 ]->handle( $request, $response );
				};
			} else
			{
				$next = function ()
				{

				};
			}
		}
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