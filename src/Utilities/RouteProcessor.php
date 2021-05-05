<?php

namespace Crossview\Exphpress\Utilities;

use \InvalidArgumentException;

class RouteProcessor
{
	protected array $acceptableDataTypes = [
		'bool',
		'float',
		'double',
		'int'
	];

	public function generateUrlDataMap( array $url ): array
	{
		$routes = [];

		for ( $i = 0; $i < count( $url ); $i++ )
		{
			$segment = $url[ $i ];
			$data    = [];
			if ( $this->isUrlParameter( $segment ) )
			{
				$data[ 'param' ] = true;
				$data[ 'path' ]  = $this->parseUrlParameterName( $segment );
				$data[ 'type' ]  = $this->parseUrlParameterTypes( $segment );
			} else
			{
				$data[ 'param' ] = false;
				$data[ 'path' ]  = $segment;
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
		return $parameter[ 0 ] === ':';
	}
}