<?php

namespace Crossview\Exphpress\Middleware;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class MiddlewareContainer
{
	/**
	 * @var MiddlewareInterface[] Container for middlewares
	 */
	protected array $middleware = [];

	/**
	 * @return MiddlewareInterface[]
	 */
	public function getMiddleware(): array
	{
		return $this->middleware;
	}

	/**
	 * @var Request Request instance
	 */
	protected Request $request;

	/**
	 * @var Response Response instance
	 */
	protected Response $response;

	public function __construct( Request $request, Response $response )
	{
		$this->request  = $request;
		$this->response = $response;
	}

	/**
	 * Registers a middleware on the container
	 *
	 * @param MiddlewareInterface $m The middleware to register
	 *
	 * @return $this
	 */
	public function register( MiddlewareInterface $m ): MiddlewareContainer
	{
		array_push( $this->middleware, $m );
		return $this;
	}

	/**
	 * Executes the middleware pipeline
	 */
	public function execute(): void
	{
		$current = array_shift( $this->middleware );
		if ( $current !== null )
		{
			$current->handle( $this->request, $this->response, function ()
			{
				$this->execute();
			} );
		}
	}
}