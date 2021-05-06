<?php

namespace Crossview\Exphpress\Routing;

use \Closure;
use Crossview\Exphpress\Utilities\RouteProcessor;
use \InvalidArgumentException;
use Crossview\Exphpress\Utilities\CanProcessPaths;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class Route
{
	use CanProcessPaths;

	/**
	 * @var string The URL this Route represents
	 */
	protected string $route;

	/**
	 * @var array Route::route parsed as an array
	 */
	protected array $parsedRoute;

	/**
	 * Getter for Route::$parsedRoute
	 *
	 * @return RouteSegmentData[]
	 */
	public function getParsedRoute(): array
	{
		return $this->parsedRoute;
	}

	/**
	 * @var array An associative array representing HTTP method => handler pairs
	 */
	protected array $handlers;

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
	 * @var string[] An array representing valid HTTP methods. This array also includes 'ANY', which is used to respond to an otherwise-unhandled HTTP method
	 */
	protected array $methods = [
		'ANY',
		'CONNECT',
		'DELETE',
		'GET',
		'HEAD',
		'OPTIONS',
		'PATCH',
		'POST',
		'PUT',
		'TRACE'
	];

	/**
	 * Route constructor.
	 *
	 * @param string $route The URI this Route represents
	 */
	public function __construct( string $route )
	{
		$this->route    = $route;
		$this->handlers = [];

		// Parse the path and set up route parameters
		$explodedRoute     = $this->processPath( $route );
		$matcher           = new RouteProcessor;
		$this->parsedRoute = $matcher->generateUrlDataMap( $explodedRoute );
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
	 * Gets the handler for a specifc method, or null if not set
	 *
	 * @param string $method
	 *
	 * @return Closure|null
	 */
	public function getHandler( string $method ): ?Closure
	{
		if ( isset( $this->handlers[ $method ] ) )
		{
			return $this->handlers[ $method ];
		}

		return null;
	}

	/**
	 * Adds a route handler generically
	 *
	 * This method works under the hood of each HTTP verb method to add a route handler in a generic way.
	 *
	 * @param string  $method  The HTTP verb to respond to
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

	public function any( Closure $handler ): Route
	{
		return $this->addHandler( 'ANY', $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for CONNECT requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 *
	 * @return $this
	 */
	public function connect( Closure $handler ): Route
	{
		return $this->addHandler( 'CONNECT', $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for DELETE requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 *
	 * @return $this
	 */
	public function delete( Closure $handler ): Route
	{
		return $this->addHandler( 'DELETE', $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for GET requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 *
	 * @return $this
	 */
	public function get( Closure $handler ): Route
	{
		return $this->addHandler( 'GET', $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for HEAD requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 *
	 * @return $this
	 */
	public function head( Closure $handler ): Route
	{
		return $this->addHandler( 'HEAD', $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for OPTIONS requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 *
	 * @return $this
	 */
	public function options( Closure $handler ): Route
	{
		return $this->addHandler( 'OPTIONS', $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for PATCH requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 *
	 * @return $this
	 */
	public function patch( Closure $handler ): Route
	{
		return $this->addHandler( 'PATCH', $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for POST requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 *
	 * @return $this
	 */
	public function post( Closure $handler ): Route
	{
		return $this->addHandler( 'POST', $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for PUT requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 *
	 * @return $this
	 */
	public function put( Closure $handler ): Route
	{
		return $this->addHandler( 'PUT', $handler );
	}

	/**
	 * A wrapper function around Route::addHandler for TRACE requests
	 *
	 * @param Closure $handler the function to execute when the Route is accessed
	 *
	 * @return $this
	 */
	public function trace( Closure $handler ): Route
	{
		return $this->addHandler( 'TRACE', $handler );
	}

	private function generateAllowHandler(): Closure
	{
		$methods = join( ', ', array_keys( $this->handlers ) );

		return function ( Request $request, Response $response ) use ( $methods )
		{
			$response->status( 405 )
					 ->setHeader( 'Allow', $methods )
					 ->send();
		};
	}

	/**
	 * Executes a handler based on the current HTTP verb
	 *
	 * If no handler is registered for the current HTTP verb, the 'all' handler is fired. If no 'all' handler is registered, a 405 response is sent along with an Allow header containing each of the HTTP verbs with a registered handler.
	 *
	 * @param Request  $request
	 * @param Response $response
	 */
	public function execute( Request $request, Response $response )
	{
		$handler = $this->getHandler( $request->getMethod() )
			?? $this->getHandler( 'ANY' )
			?? $this->generateAllowHandler();

		$handler( $request, $response );
	}
}