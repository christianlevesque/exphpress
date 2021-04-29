<?php

namespace Exphpress;

class Route
{
	/**
	 * @var string The URI this Route represents
	 */
	private $route;

	/**
	 * @var array An associative array representing HTTP method callback pairs
	 */
	private $handlers;

	/**
	 * Route constructor.
	 *
	 * @param string  $route      The URI this Route represents
	 * @param mixed[] $handlers   [optional] An associative array representing HTTP method => callback
	 *                            pairs.
	 *
	 * @throws Exception @see Route::verifyHandlers()
	 */
	public function __construct( string $route, $handlers = null )
	{
		$this->route = $route;
		if ( !empty( $handlers ) )
		{
			$this->verifyHandlers( $handlers );
			$this->handlers = $handlers;
		}
		else
		{
			$this->handlers = array();
		}

	}

	/**
	 * __toString() magic method
	 *
	 * @return string The value of the $route field
	 */
	public function __toString()
	{
		return $this->route;
	}

	/**
	 * __call() magic method
	 *
	 * Route uses the __call() method to allow developers to register callbacks for HTTP verbs without needing to
	 * define a method for each HTTP verb. An obvious drawback to this approach is that if the developer misspells the
	 * verb (e.g., "deleet" instead of "delete"), the callback for that verb will be inaccessible and there will be no
	 * error message.
	 *
	 * @param $verb
	 * @param $arguments
	 *
	 * @return Route
	 * @throws AppException
	 */
	public function __call( $verb, $arguments )
	{
		$verb = strtoupper( $verb );

		return $this->addHandler( $verb, $arguments[ 0 ] );
	}

	/**
	 * Verifies the validity of a route handler
	 *
	 * Looks at the values passed and ensures that they are of the right type and that the selected HTTP verb has not
	 * already been used. This last behavior can be overriden by setting $suppressDuplicateKeyException to true.
	 *
	 * @param array   $handlers                      An associative array of HTTP method => callback pairs
	 * @param boolean $suppressDuplicateKeyException (optional) A flag indicating that an exception should NOT be
	 *                                               thrown in the event that a callback already exists for a
	 *                                               particular HTTP method. If set to true, the exception is
	 *                                               suppressed and the existing callback is overwritten. If not set,
	 *                                               defaults to false
	 *
	 * @throws AppException if HTTP method is not a string
	 * @throws AppException if provided callback is not callable
	 * @throws AppException if HTTP method already has a valid callback; this can be suppressed by setting the optional
	 *                   $suppressDuplicateKeyException to true
	 */
	private function verifyHandlers( $handlers, $suppressDuplicateKeyException = false )
	{
		foreach ( $handlers as $method => $callback )
		{
			if ( !is_string( $method ) )
				throw new AppException(
					"The HTTP Method provided to the route '{$this->route}' must be a string; '" . gettype(
						$method
					) . "' provided",
					500
				);

			if ( !is_callable( $callback ) )
				throw new AppException(
					"The callback provided to the route '{$this->route}' {$method} HTTP method must be a function; '" . gettype(
						$callback
					) . "' provided",
					500
				);

			if ( !$suppressDuplicateKeyException && isset( $this->handlers[ $method ] ) )
				throw new AppException(
					"The HTTP Request Method '{$method}' in the route '{$this->route}' already has a callback assigned",
					500
				);

		}
	}

	/**
	 * Gets the complete list of route handlers
	 *
	 * @return array The list of route handlers as an associative array of HTTP verb => handler pairs
	 */
	public function getHandlers()
	{
		return $this->handlers;
	}

	/**
	 * Adds a route handler generically
	 *
	 * This method works under the hood of each HTTP verb method to add a route handler in a generic way.
	 *
	 * @param string   $method   The HTTP verb to respond to
	 * @param callable $callback The callback used to respond to the route via a given HTTP verb
	 *
	 * @return $this Returns the instance of the Route object (to enable method chaining)
	 * @throws AppException @see Route::verifyHandlers()
	 */
	private function addHandler( $method, $callback )
	{
		$handler = array( $method => $callback );
		$this->verifyHandlers( $handler );
		$this->handlers[ $method ] = $callback;

		return $this;
	}

	/**
	 * Executes a callback based on the current HTTP verb
	 *
	 * If no callback is registered for the current HTTP verb, the 'all' callback is fired. If no 'all' callback is
	 * registered, a 405 response is sent along with an Allow header containing each of the HTTP verbs with a
	 * registered callback.
	 *
	 * @param Request  $request
	 * @param Response $response
	 */
	public function executeCallback( Request $request, Response $response )
	{
		if ( isset( $this->handlers[ $request->method ] ) )
			$this->handlers[ $request->method ]( $request, $response );
		else if ( isset( $this->handlers[ 'ALL' ] ) )
			$this->handlers[ 'ALL' ]( $request, $response );
		else
		{
			$allowValue = '';
			$methods    = array_keys( $this->handlers );

			foreach ( $methods as $method )
			{
				$allowValue .= $method . ', ';
			}

			if ( strlen( $allowValue ) > 0 )
				$allowValue = substr( $allowValue, 0, -2 );

			$response->status( 405 )
					 ->setHeader( 'Allow', $allowValue )
					 ->send();
		}

	}
}