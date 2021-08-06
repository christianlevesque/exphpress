<?php

namespace Crossview\Exphpress\Middleware;

use Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class OutputBuffer implements Middleware
{
	/**
	 * @inheritDoc
	 */
	public function handle( Request $request, Response $response, Closure $next )
	{
		ob_start();
		$next();
		$response->send();
		ob_flush();
	}
}