<?php

namespace Crossview\Exphpress;

use \InvalidArgumentException;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\MiddlewareInterface;

class Route
{
	/**
	 * @var string The URI this Route represents
	 */
	private string $route;

	/**
	 * @var array An associative array representing HTTP method => middleware pairs
	 */
	private array $middlewares;

	/**
	 * @var string[] An array representing valid HTTP methods. This array also includes 'ANY', which is used to respond to an otherwise-unhandled HTTP method
	 */
	private array $methods = [
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
		$this->middlewares = array();
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
	 * Gets the complete list of route middlewares
	 *
	 * @return Route[] The list of route middlewares as an associative array of HTTP verb => middleware pairs
	 */
	public function getMiddlewares(): array
	{
		return $this->middlewares;
	}

	/**
	 * Adds a route middleware generically
	 *
	 * This method works under the hood of each HTTP verb method to add a route middleware in a generic way.
	 *
	 * @param string              $method     The HTTP verb to respond to
	 * @param MiddlewareInterface $middleware The middleware used to respond to the route via a given HTTP verb
	 *
	 * @return $this Returns the instance of the Route object (to enable method chaining)
	 */
	public function addMiddleware( string $method, MiddlewareInterface $middleware ): Route
	{
		if ( array_search( $method, $this->methods ) === false )
		{
			throw new InvalidArgumentException( "'$method' is not a valid HTTP method." );
		}
		$this->middlewares[ $method ] = $middleware;

		return $this;
	}

	/**
	 * A wrapper function around Route::addMiddleware for CONNECT requests
	 *
	 * @param MiddlewareInterface $middleware the function to execute when the Route is accessed
	 * @return $this
	 */
	public function connect( MiddlewareInterface $middleware ): Route
	{
		return $this->addMiddleware( "CONNECT", $middleware );
	}

	/**
	 * A wrapper function around Route::addMiddleware for DELETE requests
	 *
	 * @param MiddlewareInterface $middleware the function to execute when the Route is accessed
	 * @return $this
	 */
	public function delete( MiddlewareInterface $middleware ): Route
	{
		return $this->addMiddleware( "DELETE", $middleware );
	}

	/**
	 * A wrapper function around Route::addMiddleware for GET requests
	 *
	 * @param MiddlewareInterface $middleware the function to execute when the Route is accessed
	 * @return $this
	 */
	public function get( MiddlewareInterface $middleware ): Route
	{
		return $this->addMiddleware( "GET", $middleware );
	}

	/**
	 * A wrapper function around Route::addMiddleware for HEAD requests
	 *
	 * @param MiddlewareInterface $middleware the function to execute when the Route is accessed
	 * @return $this
	 */
	public function head( MiddlewareInterface $middleware ): Route
	{
		return $this->addMiddleware( "HEAD", $middleware );
	}

	/**
	 * A wrapper function around Route::addMiddleware for OPTIONS requests
	 *
	 * @param MiddlewareInterface $middleware the function to execute when the Route is accessed
	 * @return $this
	 */
	public function options( MiddlewareInterface $middleware ): Route
	{
		return $this->addMiddleware( "OPTIONS", $middleware );
	}

	/**
	 * A wrapper function around Route::addMiddleware for PATCH requests
	 *
	 * @param MiddlewareInterface $middleware the function to execute when the Route is accessed
	 * @return $this
	 */
	public function patch( MiddlewareInterface $middleware ): Route
	{
		return $this->addMiddleware( "PATCH", $middleware );
	}

	/**
	 * A wrapper function around Route::addMiddleware for POST requests
	 *
	 * @param MiddlewareInterface $middleware the function to execute when the Route is accessed
	 * @return $this
	 */
	public function post( MiddlewareInterface $middleware ): Route
	{
		return $this->addMiddleware( "POST", $middleware );
	}

	/**
	 * A wrapper function around Route::addMiddleware for PUT requests
	 *
	 * @param MiddlewareInterface $middleware the function to execute when the Route is accessed
	 * @return $this
	 */
	public function put( MiddlewareInterface $middleware ): Route
	{
		return $this->addMiddleware( "PUT", $middleware );
	}

	/**
	 * A wrapper function around Route::addMiddleware for TRACE requests
	 *
	 * @param MiddlewareInterface $middleware the function to execute when the Route is accessed
	 * @return $this
	 */
	public function trace( MiddlewareInterface $middleware ): Route
	{
		return $this->addMiddleware( "TRACE", $middleware );
	}

	/**
	 * Executes a middleware based on the current HTTP verb
	 *
	 * If no middleware is registered for the current HTTP verb, the 'all' middleware is fired. If no 'all' middleware is registered, a 405 response is sent along with an Allow header containing each of the HTTP verbs with a registered middleware.
	 *
	 * @param Request  $request
	 * @param Response $response
	 */
	public function executemiddleware( Request $request, Response $response )
	{
		if ( isset( $this->middlewares[ $request->method ] ) )
		{
			$this->middlewares[ $request->method ]( $request, $response );
		} else
		{
			if ( isset( $this->middlewares[ 'ALL' ] ) )
			{
				$this->middlewares[ 'ALL' ]( $request, $response );
			} else
			{
				$allowValue = '';
				$methods    = array_keys( $this->middlewares );

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