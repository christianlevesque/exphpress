<?php

namespace Crossview\Exphpress\Middleware;

use Closure;
use Exception;
use Crossview\Exphpress\Exceptions\ExphpressException;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class ErrorHandling implements Middleware
{

	/**
	 * @inheritDoc
	 */
	public function handle( Request $request, Response $response, Closure $next )
	{
		try
		{
			$next();
		} catch ( Exception $e )
		{
			ob_clean();
			$error = [
				'message' => $e->getMessage(),
				'trace'   => $e->getTraceAsString(),
				'code'    => $e->getCode()
			];
			$response->status( 500 )
					 ->send( json_encode( $error ) );
		}
	}
}