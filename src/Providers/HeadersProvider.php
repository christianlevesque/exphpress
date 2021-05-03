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
	 *
	 * After headers have been sent, the header buffer is flushed. The terminal middleware is then responsible for calling HeadersProvider::sendHeaders again, to allow any middleware that queued headers after calling $next() to send cookies using the Request instance.
	 */
	public function sendHeaders(): void
	{
		// using map instead of a foreach loop because of an apparent bug that throws off the code path count for foreach loops
		array_map(
			function ( $name, $value )
			{
				header( "$name: $value" );
			},
			array_keys( $this->values ),
			$this->values
		);

		$this->values = [];
	}
}