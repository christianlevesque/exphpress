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
	private array $middleware = [];

	/**
	 * @var Closure The middleware pipeline
	 */
	private Closure $pipeline;

	public function __construct()
	{
		// The pipeline starts with an empty closure
		// This allows developers to create their middleware without worrying about a null check on next(), because it's guaranteed to never be null
		// I'll balance a single unnecessary function call over potentially dozens or hundreds of null checks
		$this->pipeline = function ()
		{
		};
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
	 * Executes all middlewares registered on the container
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

		$this->pipeline = $pipeline;

		return $this;
	}

	public function execute(): void
	{
		( $this->pipeline )();
	}
}