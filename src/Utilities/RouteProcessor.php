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
		'number',
		'string'
	];

	/**
	 * Determines if a Request matches a Route
	 *
	 * This method only determines if the Request matches the path data supplied to a given Route - it does NOT determine if there is an appropriate handler for the current request method. That logic is contained within Route::execute.
	 *
	 * @param Route   $route   The Route to test
	 * @param Request $request The Request to test against
	 *
	 * @return bool
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
			$currentRoute = $parsedRoute[ $i ];
			$currentUrl   = $parsedUrl[ $i ];

			// If the current Route segment is an asterisk (*) then it automatically matches the rest of the URL
			if ( $currentRoute->getPath() === "*" )
			{
				return true;
			}

			if ( $currentRoute->isParam() && $this->processDataTypes( $currentRoute, $currentUrl ) )
			{
				continue;
			}

			// if all other tests have not returned or continued, and the current portions don't match, the route and uri don't match
			if ( $currentRoute->getPath() !== $currentUrl )
			{
				return false;
			}
		}

		return true;
	}

	public function processDataTypes( RouteSegmentData $data, string $urlParam ): bool
	{
		return $this->processBool( $data, $urlParam )
			|| $this->processNumber( $data, $urlParam )
			|| $this->processString( $data, $urlParam )
			|| $this->processAny( $data, $urlParam );
	}

	public function processAny( RouteSegmentData $data, string $value ): bool
	{
		if ( array_search( 'any', $data->getTypes() ) === false )
		{
			return false;
		}

		$data->setType( 'any' );
		$data->setValue( $value );
		return true;
	}

	public function processBool( RouteSegmentData $data, string $value ): bool
	{
		if ( array_search( 'bool', $data->getTypes() ) === false )
		{
			return false;
		}

		$truthy = [
			'true',
			'1',
			'yes'
		];
		$falsy  = [
			'false',
			'0',
			'no'
		];

		if ( array_search( $value, $truthy ) !== false )
		{
			$data->setType( 'bool' );
			$data->setValue( true );
			return true;
		} else if ( array_search( $value, $falsy ) !== false )
		{
			$data->setType( 'bool' );
			$data->setValue( false );
			return true;
		}

		return false;
	}

	/**
	 * Determines whether a value matches the number rules for RouteSegmentData
	 *
	 * A value is considered a number if it can be cast to int or float.
	 *
	 * @param RouteSegmentData $data  The route data to process
	 * @param string           $value The value to process as a number
	 *
	 * @return bool
	 */
	public function processNumber( RouteSegmentData $data, string $value ): bool
	{
		if ( array_search( 'number', $data->getTypes() ) === false )
		{
			return false;
		}

		if ( $value === '0' )
		{
			$data->setType( 'number' );
			$data->setValue( 0 );
			return true;
		}

		$parsed = floatval( $value );
		if ( $parsed > 0 )
		{
			$data->setType( 'number' );
			$data->setValue( $parsed );
			return true;
		}

		return false;
	}

	/**
	 * Determines whether a value matched the string rules for RouteSegmentData
	 *
	 * A value is always considered a string, so this method merely checks for the presence of the string type, applies the correct mutations to $data and returns true. processString will only return false if RouteSegmentData doesn't include the 'string' type.
	 *
	 * @param RouteSegmentData $data  The route data to process
	 * @param string           $value The value to process as a string
	 *
	 * @return bool
	 */
	public function processString( RouteSegmentData $data, string $value ): bool
	{
		if ( array_search( 'string', $data->getTypes() ) === false )
		{
			return false;
		}

		$data->setType( 'string' );
		$data->setValue( $value );
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