<?php

namespace Crossview\Exphpress\Middleware;

use Closure;
use Exception;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class ErrorHandler implements Middleware
{
	/**
	 * @inheritDoc
	 */
	public function handle( Request $request, Response $response, Closure $next )
	{
		try {
			$next();
		} catch ( Exception $e ) {
			ob_clean();
			$error = [
				'message' => $e->getMessage(),
				'trace'   => $e->getTraceAsString(),
				'code'    => $e->getCode()
			];
			$response->status( 500 )
					 ->setHeader( 'Content-Type', 'application/json' )
					 ->setResponseBody( json_encode( $error ) );
		}
	}
}