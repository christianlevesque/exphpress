<?php

namespace Crossview\Exphpress\Middleware;

use \Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class MiddlewareContainer
{
	/**
	 * @var MiddlewareInterface[] Container for middlewares
	 */
	protected array $middleware = [];

	/**
	 * @var Closure The middleware pipeline
	 */
	protected Closure $pipeline;

	public function __construct( Closure $initialPipeline )
	{
		$this->pipeline = $initialPipeline;
	}

	/**
	 * @return MiddlewareInterface[]
	 */
	public function getMiddleware(): array
	{
		return $this->middleware;
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
		// Fill the array backwards so we don't have to reverse the array when building the pipeline
		array_unshift( $this->middleware, $m );
		return $this;
	}

	/**
	 * Constructs the middleware pipeline using all the middlewares on the container
	 *
	 * @param Request  $request  The HTTP Request
	 * @param Response $response The HTTP Response
	 *
	 * @return $this
	 */
	public function buildPipeline( Request $request, Response $response ): MiddlewareContainer
	{
		// Grab a local reference to the pipeline so the closure can pull it into scope
		$pipeline = $this->pipeline;

		// Wrap each new middleware around the existing pipeline
		foreach ( $this->middleware as $currentMiddleware )
		{
			// The closure needs to reference the current middleware, the request, the response, and the existing pipeline
			// The existing pipeline will be used as next() for the new pipeline
			$pipeline = function () use ( $currentMiddleware, &$request, &$response, $pipeline )
			{
				$currentMiddleware->handle( $request, $response, $pipeline );
			};
		}

		// Save the pipeline back to the MiddlewareContainer
		$this->pipeline = $pipeline;

		return $this;
	}

	/**
	 * Executes the middleware pipeline
	 */
	public function execute(): void
	{
		( $this->pipeline )();
	}
}