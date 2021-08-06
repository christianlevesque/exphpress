<?php

namespace Crossview\Exphpress\Routing;

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

	protected ?Route $matchedRoute = null;

	/**
	 * Getter for the current matched Route
	 *
	 * @return Route|null Returns the Route matching the current request's URI, or null if no Route has been matched
	 */
	public function getMatchedRoute(): ?Route
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