<?php

namespace Crossview\Exphpress\Middleware;

use \Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

interface Middleware
{
	/**
	 * @param Request  $request  The HTTP Request object
	 * @param Response $response The HTTP Response object
	 * @param Closure  $next     The next middleware in the pipeline
	 * @return void
	 */
	public function handle( Request $request, Response $response, Closure $next );
}