<?php

namespace Crossview\Exphpress\Middleware;

use Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Providers\CookieProvider;
use Crossview\Exphpress\Providers\HeadersProvider;

class ResponseConfigurer implements Middleware
{
	public function handle( Request $request, Response $response, Closure $next )
	{
		$this->configureProviders( $response );
		$next();
	}

	protected function configureProviders( Response $response ): ResponseConfigurer
	{
		$response->setHeadersProvider( new HeadersProvider() );
		$response->setCookieProvider( new CookieProvider() );

		return $this;
	}
}