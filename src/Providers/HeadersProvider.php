<?php

namespace Crossview\Exphpress\Providers;

class HeadersProvider
{
	private CrudArrayValueProvider $headers;

	public function __construct( array $defaultHeaders = [] )
	{
		$this->headers = new CrudArrayValueProvider( $defaultHeaders );
	}

	/**
	 * Gets a header from the queue
	 *
	 * @param string $name The name of the header to get
	 *
	 * @return mixed|null
	 */
	public function getHeader( string $name )
	{
		return $this->headers->getRaw( $name );
	}

	/**
	 * Queues a header to be sent
	 *
	 * @param string $name  The header to queue
	 * @param mixed  $value The value of the header to queue
	 *
	 * @return $this
	 */
	public function setHeader( string $name, $value ): HeadersProvider
	{
		$this->headers->set( $name, $value );

		return $this;
	}

	/**
	 * Removes a header from the queue
	 *
	 * @param string $name The header to unqueue
	 *
	 * @return $this
	 */
	public function unsetHeader( string $name ): HeadersProvider
	{
		$this->headers->unset( $name );

		return $this;
	}

	/**
	 * Sends all headers registered
	 *
	 * After headers have been sent, the header buffer is flushed. The terminal middleware is then responsible for calling HeadersProvider::sendHeaders again, to allow any middleware that queued headers after calling $next() to send cookies using the Request instance.
	 */
	public function sendHeaders(): void
	{
		$keys = array_keys( $this->headers->getAll() );
		// using for instead of a foreach loop because of an apparent bug that throws off the code path count for foreach loops
		for ( $i = 0; $i < count( $keys ); $i++ )
		{
			$name  = $keys[ $i ];
			$value = $this->headers->getRaw( $name );
			header( "$name: $value" );
		}

		$this->headers->unsetAll();
	}
}