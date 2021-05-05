<?php

namespace Crossview\Exphpress\Routing;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class Router
{
	protected static ?Router $instance;
	protected array          $uri = [];

	/**
	 * @var Route[] The Routes registered on the Router
	 */
	protected array $routes = [];

	/**
	 * Routes getter
	 *
	 * @return Route[]
	 */
	public function getRoutes(): array
	{
		return $this->routes;
	}

	protected Route $matchedRoute;

	/**
	 * Getter for the current matched Route
	 *
	 * @return Route|null Returns the Route matching the current request's URI, or null if no Route has been matched
	 */
	public function getMatchedRoute(): Route
	{
		return $this->matchedRoute;
	}

	/**
	 * Setter for the current matched Route
	 *
	 * @param Route $route
	 */
	public function setMatchedRoute( Route $route ): void
	{
		$this->matchedRoute = $route;
	}

	/**
	 * Router constructor
	 */
	protected function __construct()
	{
	}

	public static function getInstance(): Router
	{
		if ( !self::hasInstance() )
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function hasInstance(): bool
	{
		return isset( self::$instance );
	}

	public static function deleteInstance(): void
	{
		self::$instance = null;
	}

	/**
	 * Accesses a specific route
	 *
	 * This method accesses the Route object represented by the passed argument, or it creates a new Route representing the passed argument if no existing Route matches
	 *
	 * @param string $uri The URI to match for registering route handlers
	 *
	 * @return Route Returns the Route object represented by the passed $uri, or a new Route object if no matching Route exists
	 */
	public function route( string $uri ): Route
	{
		for ( $i = 0; $i < count( $this->routes ); $i++ )
		{
			if ( $uri === (string) $this->routes[ $i ] )
			{
				return $this->routes[ $i ];
			}
		}

		$routesLength = array_push( $this->routes, new Route( $uri ) );

		return $this->routes[ $routesLength - 1 ];
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
//	protected function explodeRoute( string $uri ): array
//	{
//		$hasQuestionMark = strpos( $uri, '?' );
//
//		if ( $hasQuestionMark )
//		{
//			$uri = substr( $uri, 0, $hasQuestionMark );
//		}
//
//		$lastCharacterSlash = $uri[ -1 ] === '/';
//
//		if ( $lastCharacterSlash )
//		{
//			$uri = substr( $uri, 0, -1 );
//		}
//
//		if ( strlen( $uri ) > 0 )
//		{
//			$uri = substr( $uri, 1 );
//		}
//
//		return explode( '/', $uri );
//	}
//
//	/**
//	 * Utility function to match two URIs
//	 *
//	 * Performs various tests to determine if two exploded URIs match. If the Route length has more elements than the requested URI, there is no match. If the requested URI has more elements than the Route and the last element of the Route is NOT an asterisk (*), there is no match. The rest of the tests are performed 1:1 on each element of the exploded URIs.
//	 *
//	 * If the current portion of the $route exists, but the corresponding portion of the $uri does not, it can't match.
//	 *
//	 * If The current portion of the $route is an asterisk (*), and the corresponding portion of $uri exists (we know it does because of the previous test), then it automatically matches the rest of the $uri.
//	 *
//	 * If The current portion of the $route is a parameter, and the corresponding portion of the $uri is a non-empty value, it may be a match.
//	 *
//	 * If all other 1:1 tests have not returned or continued, and the current portions don't match, then the $route and $uri don't match.
//	 *
//	 * @param array $route The exploded Route
//	 * @param array $uri   The exploded request URI
//	 *
//	 * @return array|bool Returns an array containing any parameters matched by the URI. If no parameters were matched but the URI and Route match, the array will be empty. If the URI and Route don't match, returns false.
//	 */
//	protected function matchRoute( array $route, array $uri )
//	{
//		$parameters  = [];
//		$routeLength = count( $route );
//		$uriLength   = count( $uri );
//
//		if ( $routeLength > $uriLength )
//		{
//			return false;
//		}
//
//		if ( $uriLength > $routeLength && $route[ $routeLength - 1 ] !== "*" )
//		{
//			return false;
//		}
//
//		for ( $i = 0; $i < count( $route ); $i++ )
//		{
//			if ( !isset( $uri[ $i ] ) )
//			{
//				return false;
//			}
//
//			if ( $route[ $i ] === "*" )
//			{
//				break;
//			}
//
//			if ( !empty( $route[ $i ] ) && $route[ $i ][ 0 ] === ":" && !empty( $uri[ $i ] ) )
//			{
//				$parameter                = substr( $route[ $i ], 1 );
//				$parameters[ $parameter ] = $uri[ $i ];
//				continue;
//			}
//
//			// if all other tests have not returned or continued, and the current portions don't match, the route and uri don't match
//			if ( $route[ $i ] !== $uri[ $i ] )
//			{
//				return false;
//			}
//		}
//
//		return $parameters;
//	}
//
//	/**
//	 * Public method to execute Route matches
//	 *
//	 * Calls all protected methods used to match a Route to the request URI.
//	 *
//	 * @param Request  $request
//	 * @param Response $response
//	 */
//	public function executeRouteMatch( Request $request, Response $response )
//	{
//		$this->uri = $this->explodeRoute( $_SERVER[ 'REQUEST_URI' ] );
//
//		foreach ( $this->routes as $route )
//		{
//			$explodedRoute = $this->explodeRoute( (string) $route );
//			$parameters    = $this->matchRoute( $explodedRoute, $this->uri );
//
//			// if a route doesn't match, $parameters will be false. if it does, $parameters will be an array containing any matched parameters in the URI, so go ahead and save data and break out of the loop if $parameters isn't explicitly false
//			if ( $parameters !== false )
//			{
//				$this->matchedRoute = $route;
//				$this->parameters   = !empty( $parameters )
//					? $parameters
//					: [];
//				break;
//			}
//		}
//
//		if ( isset( $this->matchedRoute ) )
//		{
//			$this->matchedRoute->execute( $request, $response );
//		}
//	}
}