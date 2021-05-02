<?php

namespace Crossview\Exphpress;

use \Closure;
use \InvalidArgumentException;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class Route
{
	/**
	 * @var string The URI this Route represents
	 */
	protected string $route;

	/**
	 * @var array An associative array representing HTTP method => handler pairs
	 */
	protected array $handlers;

	/**
	 * @var string[] An array representing valid HTTP methods. This array also includes 'ANY', which is used to respond to an otherwise-unhandled HTTP method
	 */
	protected array $methods = [
		"ANY",
		"CONNECT",
		"DELETE",
		"GET",
		"HEAD",
		"OPTIONS",
		"PATCH",
		"POST",
		"PUT",
		"TRACE"
	];

	/**
	 * Route constructor.
	 *
	 * @param string $route The URI this Route represents
	 */
	public function __construct( string $route )
	{
		$this->route       = $route;
		$this->handlers = array();
	}

	/**
	 * __toString() magic method
	 *
	 * @return string The value of the $route field
	 */
	public function __toString(): string
	{
		return $this->route;
	}

	/**
	 * Gets the complete list of route handlers
	 *
	 * @return array The list of route handlers as an associative array of HTTP verb => handler pairs
	 */
	public function getHandlers(): array
	{
		return $this->handlers;
	}

	/**
	 * Adds a route handler generically
	 *
	 * This method works under the hood of each HTTP verb method to add a route handler in a generic way.
	 *
	 * @param string              $method     The HTTP verb to respond to
	 * @param Closure $handler The handler used to respond to the route via a given HTTP verb
	 *
	 * @return $this Returns the instance of the Route object (to enable method chaining)
	 */
	public function addHandler( string $method, Closure $handler ): Route
	{
		if ( array_search( $method, $this->methods ) === false )
		{
			throw new InvalidArgumentException( "'$method' is not a valid HTTP method." );
		}
		$this->handlers[ $method ] = $handler;

		return $this;
	}

	/**
	 * A wrapper function around Route::addHandler for CONNECT requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 * @return $this
	 */
	public function connect( Closure $handler ): Route
	{
		return $this->addHandler( "CONNECT", $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for DELETE requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 * @return $this
	 */
	public function delete( Closure $handler ): Route
	{
		return $this->addHandler( "DELETE", $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for GET requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 * @return $this
	 */
	public function get( Closure $handler ): Route
	{
		return $this->addHandler( "GET", $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for HEAD requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 * @return $this
	 */
	public function head( Closure $handler ): Route
	{
		return $this->addHandler( "HEAD", $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for OPTIONS requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 * @return $this
	 */
	public function options( Closure $handler ): Route
	{
		return $this->addHandler( "OPTIONS", $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for PATCH requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 * @return $this
	 */
	public function patch( Closure $handler ): Route
	{
		return $this->addHandler( "PATCH", $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for POST requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 * @return $this
	 */
	public function post( Closure $handler ): Route
	{
		return $this->addHandler( "POST", $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for PUT requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 * @return $this
	 */
	public function put( Closure $handler ): Route
	{
		return $this->addHandler( "PUT", $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for TRACE requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 * @return $this
	 */
	public function trace( Closure $handler ): Route
	{
		return $this->addHandler( "TRACE", $handler );
	}

	/**
	 * Executes a handler based on the current HTTP verb
	 *
	 * If no handler is registered for the current HTTP verb, the 'all' handler is fired. If no 'all' handler is registered, a 405 response is sent along with an Allow header containing each of the HTTP verbs with a registered handler.
	 *
	 * @param Request  $request
	 * @param Response $response
	 */
	public function executehandler( Request $request, Response $response )
	{
		if ( isset( $this->handlers[ $request->method ] ) )
		{
			$this->handlers[ $request->method ]( $request, $response );
		} else
		{
			if ( isset( $this->handlers[ 'ALL' ] ) )
			{
				$this->handlers[ 'ALL' ]( $request, $response );
			} else
			{
				$allowValue = '';
				$methods    = array_keys( $this->handlers );

				foreach ( $methods as $method )
				{
					$allowValue .= $method . ', ';
				}

				if ( strlen( $allowValue ) > 0 )
				{
					$allowValue = substr( $allowValue, 0, -2 );
				}

				$response->status( 405 )
						 ->setHeader( 'Allow', $allowValue )
						 ->send();
			}
		}
	}
}