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
		// TODO: Update App to have a Closure $errorHandler on it, and simply call that here. Then default the App::$errorHandler to this code, or something substantially similar
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