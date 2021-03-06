<?php

namespace Crossview\Exphpress\Middleware;

use Closure;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Providers\ArrayValueProvider;
use Crossview\Exphpress\Providers\WritableArrayValueProvider;

class RequestConfigurer implements Middleware
{
	protected string $input;

	/**
	 * @return mixed|string
	 */
	public function getInput(): string
	{
		return $this->input;
	}

	/**
	 * @param mixed|string $input
	 */
	public function setInput( string $input ): void
	{
		$this->input = $input;
	}

	public function __construct( $input = '' )
	{
		$this->input = $input;
	}

	public function handle( Request $request, Response $response, Closure $next )
	{
		$this->configureProviders( $request )
			 ->configureUrl( $request );

		$next();
	}

	protected function configureProviders( Request $request ): RequestConfigurer
	{
		$request->setServerProvider( new ArrayValueProvider( $_SERVER ) );
		$request->setFileProvider( new ArrayValueProvider( $_FILES ) );
		$request->setCookieProvider( new ArrayValueProvider( $_COOKIE ) );

		// Regardless of the HTTP method, $_GET always contains URL query parameters
		$request->setQueryParameterProvider( new WritableArrayValueProvider( $_GET ) );

		// Determine what type of input we have and parse it
		if ($request->getServerParameter( 'CONTENT_TYPE' ) === 'application/json') {
			$parsed = json_decode( $this->input, true, $flags = JSON_THROW_ON_ERROR ) ?? [];
		} else {
			parse_str($this->input, $parsed);
		}

		$request->setRequestParameterProvider(new WritableArrayValueProvider($parsed));

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