<?php

namespace Crossview\Exphpress\Middleware;

use \Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

interface MiddlewareInterface
{
	/**
	 * @param Request      $request  The HTTP Request object
	 * @param Response     $response The HTTP Response object
	 * @param Closure|null $next     The next middleware in the pipeline wrapped in a Closure if it exists, null otherwise
	 * @return void
	 */
	public function handle( Request $request, Response $response, Closure $next = null );
}