<?php

namespace Exphpress;

class Router
{
	private static array $uri          = [];
	private static array $routes       = [];
	private static       $matchedRoute = null;
	private static       $parameters   = null;
	private static       $instance     = null;

	/**
	 * Router constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Accesses a specific route
	 *
	 * This method accesses the Route object represented by the passed argument, or it creates a new Route representing the passed argument if no existing Route matches
	 *
	 * @param string $uri The URI to match for registering route handlers
	 *
	 * @return \Exphpress\Route Returns the Route object represented by the passed $uri, or a new Route object if no matching Route exists
	 *
	 * @throws \Exception @see Route::verifyHandlers()
	 */
	public static function route( $uri )
	{
		foreach ( self::$routes as $route ) {
			if ( $uri == (string) $route ) {
				return $route;
			}
		}

		$routesLength = array_push( self::$routes, new Route( $uri ) );

		return self::$routes[ $routesLength - 1 ];
	}

	/**
	 * Getter for the current matched Route
	 *
	 * @return Route|null Returns the Route matching the current request's URI, or null if no Route has been matched
	 */
	public static function getMatchedRoute()
	{
		return self::$matchedRoute;
	}

	/**
	 * Getter for the URI parameter values
	 *
	 * @return array|null Returns an associative array of the parameter values for the current request as parameter => value pairs, or null if either the current request has no parameters or if no Route has been matched
	 */
	public static function getParameters()
	{
		return self::$parameters;
	}

	/**
	 * Utility method to explode a URI
	 *
	 * Strips any GET parameters, up to and including the question mark. Strips any trailing slash to prevent an empty last element of the array. Strips any leading slash to prevent an empty first element of the array.
	 *
	 * @param string $uri The URI to explode
	 *
	 * @return array Returns the exploded URI. If the Route or requested URI is for the home directory ('/'), the array will be empty
	 */
	private static function explodeRoute( $uri )
	{
		$hasQuestionMark = strpos( $uri, '?' );

		if ( $hasQuestionMark ) {
			$uri = substr( $uri, 0, $hasQuestionMark );
		}

		$lastCharacterSlash = $uri[ -1 ] === '/';

		if ( $lastCharacterSlash ) {
			$uri = substr( $uri, 0, -1 );
		}

		if ( strlen( $uri ) > 0 ) {
			$uri = substr( $uri, 1 );
		}

		return explode( '/', $uri );
	}

	/**
	 * Utility function to match two URIs
	 *
	 * Performs various tests to determine if two exploded URIs match. If the Route length has more elements than the requested URI, there is no match. If the requested URI has more elements than the Route and the last element of the Route is NOT an asterisk (*), there is no match. The rest of the tests are performed 1:1 on each element of the exploded URIs.
	 *
	 * If the current portion of the $route exists, but the corresponding portion of the $uri does not, it can't match.
	 *
	 * If The current portion of the $route is an asterisk (*), and the corresponding portion of $uri exists (we know it does because of the previous test), then it automatically matches the rest of the $uri.
	 *
	 * If The current portion of the $route is a parameter, and the corresponding portion of the $uri is a non-empty value, it may be a match.
	 *
	 * If all other 1:1 tests have not returned or continued, and the current portions don't match, then the $route and $uri don't match.
	 *
	 * @param array $route The exploded Route
	 * @param array $uri The exploded request URI
	 *
	 * @return array|bool Returns an array containing any parameters matched by the URI. If no parameters were matched but the URI and Route match, the array will be empty. If the URI and Route don't match, returns false.
	 */
	private static function matchRoute( $route, $uri )
	{
		$parameters  = [];
		$routeLength = count( $route );
		$uriLength   = count( $uri );

		if ( $routeLength > $uriLength ) {
			return false;
		}

		if ( $uriLength > $routeLength && $route[ $routeLength - 1 ] !== "*" ) {
			return false;
		}

		for ( $i = 0; $i < count( $route ); $i++ ) {
			if ( !isset( $uri[ $i ] ) ) {
				return false;
			}

			if ( $route[ $i ] === "*" ) {
				break;
			}

			if ( !empty( $route[ $i ] ) && $route[ $i ][ 0 ] === ":" && !empty( $uri[ $i ] ) ) {
				$parameter                = substr( $route[ $i ], 1 );
				$parameters[ $parameter ] = $uri[ $i ];
				continue;
			}

			// if all other tests have not returned or continued, and the current portions don't match, the route and uri don't match
			if ( $route[ $i ] !== $uri[ $i ] ) {
				return false;
			}
		}

		return $parameters;
	}

	/**
	 * Public method to execute Route matches
	 *
	 * Calls all private methods used to match a Route to the request URI.
	 */
	public static function executeRouteMatch()
	{
		self::$uri = self::explodeRoute( $_SERVER[ 'REQUEST_URI' ] );

		foreach ( self::$routes as $route ) {
			$explodedRoute = self::explodeRoute( (string) $route );
			$parameters    = self::matchRoute( $explodedRoute, self::$uri );

			// if a route doesn't match, $parameters will be false. if it does, $parameters will be an array containing any matched parameters in the URI, so go ahead and save data and break out of the loop if $parameters isn't explicitly false
			if ( $parameters !== false ) {
				self::$matchedRoute = $route;
				self::$parameters   = !empty( $parameters ) ? $parameters : null;
				break;
			}
		}
	}
}