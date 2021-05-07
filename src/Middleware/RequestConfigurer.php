<?php


namespace Crossview\Exphpress\Middleware;


use Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Providers\ArrayValueProvider;
use Crossview\Exphpress\Providers\WritableArrayValueProvider;

class RequestConfigurer implements Middleware
{
	public function handle( Request $request, Response $response, Closure $next )
	{
		$this->configureProviders( $request )
			 ->configureUrl( $request );

		$next();
	}

	protected function configureProviders( Request $request ): RequestConfigurer
	{
		$request->setServerProvider( new ArrayValueProvider( $_SERVER ) );
		$request->setCookieProvider( new ArrayValueProvider( $_COOKIE ) );

		// Regardless of the HTTP method, $_GET always contains URL query parameters
		$request->setQueryParameterProvider( new WritableArrayValueProvider( $_GET ) );
		$request->setRequestParameterProvider( new WritableArrayValueProvider( [] ) );

		return $this;
	}

	protected function configureUrl( Request $request ): RequestConfigurer
	{
		$request->setOriginalUrl( $request->getServerParameter( 'REQUEST_URI' ) );
		$path = explode( '?', $request->getOriginalUrl() )[ 0 ];
		$request->setPath( $path );

		return $this;
	}
}