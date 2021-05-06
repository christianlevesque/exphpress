<?php

namespace Crossview\Exphpress\Utilities;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Routing\Route;
use Crossview\Exphpress\Routing\RouteSegmentData;
use \InvalidArgumentException;

class RouteProcessor
{
	protected array $acceptableDataTypes = [
		'bool',
		'float',
		'double',
		'int'
	];

	/**
	 * Determines if a Request matches a Route
	 *
	 * .
	 *
	 * If The current portion of the $route is a parameter, and the corresponding portion of the $uri is a non-empty value, it may be a match.
	 *
	 * If all other 1:1 tests have not returned or continued, and the current portions don't match, then the $route and $uri don't match.
	 *
	 * @param Route   $route   The Route to test
	 * @param Request $request The Request to test against
	 *
	 * @return array|bool Returns an array containing any parameters matched by the URI. If no parameters were matched but the URI and Route match, the array will be empty. If the URI and Route don't match, returns false.
	 */
	public function routeMatches( Route $route, Request $request ): bool
	{
		$parsedRoute = $route->getParsedRoute();
		$parsedUrl   = $request->getParsedPath();
		$routeLength = count( $parsedRoute );
		$urlLength   = count( $parsedUrl );

		// If the Route length has more elements than the requested URL, there is no match
		if ( $routeLength > $urlLength )
		{
			return false;
		}

		// If the requested URL has more elements than the Route and the last element of the Route is NOT an asterisk (*), there is no match
		if ( $urlLength > $routeLength && $parsedRoute[ $routeLength - 1 ]->getPath() !== "*" )
		{
			return false;
		}

		for ( $i = 0; $i < $routeLength; $i++ )
		{
			$current = $parsedRoute[ $i ];
			// If the current Route segment is an asterisk (*) then it automatically matches the rest of the URL
			if ( $current->getPath() === "*" )
			{
				return true;
			}

			if ( $current->isParam() )
			{
				// TODO: match the parameters instead of just assuming they might match
				continue;
			}

			// if all other tests have not returned or continued, and the current portions don't match, the route and uri don't match
			if ( $current->getPath() !== $parsedUrl[ $i ] )
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Generates the data mapping for a Route's URL
	 *
	 * @param array $url The Route's processed path
	 *
	 * @return RouteSegmentData[]
	 */
	public function generateUrlDataMap( array $url ): array
	{
		$routes = [];

		for ( $i = 0; $i < count( $url ); $i++ )
		{
			$segment = $url[ $i ];
			$data    = new RouteSegmentData();
			if ( $this->isUrlParameter( $segment ) )
			{
				$data->setParam( true );
				$data->setPath( $this->parseUrlParameterName( $segment ) );
				$data->setTypes( $this->parseUrlParameterTypes( $segment ) );
			} else
			{
				$data->setPath( $segment );
			}

			$routes[] = $data;
		}

		return $routes;
	}

	public function validateUrlParameterTypes( array $types ): array
	{
		return array_map( function ( $type )
		{
			if ( array_search( $type, $this->acceptableDataTypes ) === false )
			{
				throw new InvalidArgumentException( $type . ' is not a valid parameter data type. Valid types are ' . join( ', ', $this->acceptableDataTypes ) );
			}

			return $type;
		}, $types );
	}

	public function parseUrlParameterTypes( string $parameter ): array
	{
		preg_match( '/.*<(.+)>/', $parameter, $matches );

		if ( !array_key_exists( 1, $matches ) )
		{
			return [ 'any' ];
		}

		return $this->validateUrlParameterTypes( explode( '|', $matches[ 1 ] ) );
	}

	/**
	 * Returns the name of a url parameter.
	 *
	 * @param string $parameter
	 *
	 * @return string
	 *
	 * @throws InvalidArgumentException if $parameter does not start with a colon
	 * @throws InvalidArgumentException if $parameter name evaluates to empty
	 */
	public function parseUrlParameterName( string $parameter ): string
	{
		if ( $parameter[ 0 ] !== ':' )
		{
			throw new InvalidArgumentException( "Route parameter $parameter must start with a leading colon (:)" );
		}

		$leftBracketPos = strpos( $parameter, '<' );
		if ( $leftBracketPos !== false )
		{
			$parameter = substr( $parameter, 1, $leftBracketPos - 1 );
		} else
		{
			$parameter = substr( $parameter, 1 );
		}

		if ( $parameter === '' )
		{
			throw new InvalidArgumentException( 'Parameter name cannot be empty' );
		}

		return $parameter;
	}

	/**
	 * Determines whether a string is a valid URL parameter name
	 *
	 * This function only looks for the presence of a leading colon character (:). More rigorous checks are done during route parameter name parsing.
	 *
	 * @param string $parameter The string to test
	 *
	 * @return bool
	 */
	public function isUrlParameter( string $parameter ): bool
	{
		if ( strlen( $parameter ) === 0 )
		{
			return false;
		}

		return $parameter[ 0 ] === ':';
	}
}