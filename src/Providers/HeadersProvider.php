<?php

namespace Crossview\Exphpress\Providers;

class HeadersProvider extends WritableArrayValueProvider implements CrudProvider
{
	public function __construct( array $defaultHeaders = [] )
	{
		parent::__construct( $defaultHeaders );
	}

	function unset( string $key ): HeadersProvider
	{
		unset( $this->values[ $key ] );
		return $this;
	}

	/**
	 * Sends all headers registered
	 */
	public function sendHeaders(): void
	{
		array_map(
			function ( $name, $value )
			{
				header( "$name: $value" );
			},
			array_keys( $this->values ),
			$this->values
		);
	}
}