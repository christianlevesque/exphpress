<?php

namespace Crossview\Exphpress\Http;

use Crossview\Exphpress\Exceptions\ExphpressException;
use Crossview\Exphpress\Providers\ReadableProvider;
use Crossview\Exphpress\Providers\ReadableWritableProvider;

class Request
{
	/**
	 * @var ReadableProvider Represents the $_SERVER array
	 */
	protected ReadableProvider $serverProvider;

	/**
	 * Gets the registered Server Provider
	 *
	 * @return ReadableProvider|null
	 */
	public function getServerProvider(): ?ReadableProvider
	{
		if ( !isset( $this->serverProvider ) )
		{
			throw new ExphpressException( 'You are attempting to access the Request ServerProvider, but none has been configured.' );
		}

		return $this->serverProvider;
	}

	/**
	 * Registers the Server Provider
	 *
	 * This method only sets the Server Provider if it has not already been set. Exphpress-provided middleware calls this method, so there's no reason for developers to call it.
	 *
	 * @param ReadableProvider $provider The Server Provider to register
	 *
	 * @return $this
	 */
	public function setServerProvider( ReadableProvider $provider ): Request
	{
		if ( isset( $this->serverProvider ) )
		{
			throw new ExphpressException( 'You are attempting to set the Request ServerProvider, but a ServerProvider has already been configured.' );
		}

		$this->serverProvider = $provider;
		return $this;
	}

	/**
	 * @var ReadableProvider Represents the $_COOKIE array
	 */
	protected ReadableProvider $cookieProvider;

	/**
	 * Gets the Cookie Provider
	 *
	 * @return ReadableProvider|null
	 */
	public function getCookieProvider(): ?ReadableProvider
	{
		if ( !isset( $this->cookieProvider ) )
		{
			throw new ExphpressException( 'You are attempting to access the Request CookieProvider, but none has been configured.' );
		}

		return $this->cookieProvider;
	}

	/**
	 * Registers the Cookie Provider
	 *
	 * This method only sets the Cookie Provider if it has not already been set. Exphpress does not provide a default Cookie Provider; a Cookie Provider must be configured here before using cookies in the Request.
	 *
	 * @param ReadableProvider $provider The Cookie Provider to register
	 *
	 * @return $this
	 */
	public function setCookieProvider( ReadableProvider $provider ): Request
	{
		if ( isset( $this->cookieProvider ) )
		{
			throw new ExphpressException( 'You are attempting to set the Request CookieProvider, but a CookieProvider has already been configured.' );
		}

		$this->cookieProvider = $provider;
		return $this;
	}

	/**
	 * @var ReadableWritableProvider A provider for URL query parameters
	 */
	protected ReadableWritableProvider $queryParameterProvider;

	/**
	 * Gets the Query Parameter Provider
	 *
	 * @return ReadableWritableProvider|null
	 */
	public function getQueryParameterProvider(): ?ReadableWritableProvider
	{
		if ( !isset( $this->queryParameterProvider ) )
		{
			throw new ExphpressException( 'You are attempting to access the Request QueryParameterProvider, but none has been configured.' );
		}

		return $this->queryParameterProvider;
	}

	/**
	 * Registers the Query Parameter Provider
	 *
	 * This method only sets the Query Parameter Provider if it has not already been set. Exphpress-provided middleware calls this method, so there's no reason for developers to call it.
	 *
	 * @param ReadableWritableProvider $provider The Query Parameter Provider to register
	 *
	 * @return $this
	 */
	public function setQueryParameterProvider( ReadableWritableProvider $provider ): Request
	{
		if ( isset( $this->queryParameterProvider ) )
		{
			throw new ExphpressException( 'You are attempting to set the Request QueryParameterProvider, but a QueryParameterProvider has already been configured.' );
		}

		$this->queryParameterProvider = $provider;
		return $this;
	}

	/**
	 * @var ReadableWritableProvider A provider for request body parameters
	 */
	protected ReadableWritableProvider $requestParameterProvider;

	/**
	 * Gets the Request Parameter Provider
	 *
	 * @return ReadableWritableProvider|null
	 */
	public function getRequestParameterProvider(): ?ReadableWritableProvider
	{
		if ( !isset( $this->requestParameterProvider ) )
		{
			throw new ExphpressException( 'You are attempting to access the Request RequestParameterProvider, but none has been configured.' );
		}

		return $this->requestParameterProvider;
	}

	/**
	 * Registers the Request Parameter Provider
	 *
	 * This method only sets the Request Parameter Provider if it has not already been set. Exphpress-provided middleware calls this method, so there's no reason for developers to call it.
	 *
	 * @param ReadableWritableProvider $provider The Request Parameter Provider to register
	 *
	 * @return $this
	 */
	public function setRequestParameterProvider( ReadableWritableProvider $provider ): Request
	{
		if ( isset( $this->requestParameterProvider ) )
		{
			throw new ExphpressException( 'You are attempting to set the Request RequestParameterProvider, but a RequestParameterProvider has already been configured.' );
		}

		$this->requestParameterProvider = $provider;
		return $this;
	}

	/**
	 * Fetches the HTTP request method for the current request
	 *
	 * @return string
	 */
	public function getMethod(): string
	{
		return $this->serverProvider->get( 'REQUEST_METHOD' );
	}

	/**
	 * Getter for the passed $_SERVER value
	 *
	 * @param string $parameter The $_SERVER value to retrieve
	 *
	 * @return mixed|null
	 */
	public function getServerParameter( string $parameter )
	{
		return $this->serverProvider->getRaw( $parameter );
	}

	/**
	 * Getter for the passed URI parameter
	 *
	 * If the field is present in the URL query parameters, its value is returned as it appears. If not, null is returned.
	 *
	 * @param string $parameter The URI query parameter to retrieve
	 *
	 * @return mixed|null
	 *
	 * @see ReadableProvider::getRaw
	 */
	public function getQueryParameter( string $parameter )
	{
		return $this->queryParameterProvider->getRaw( $parameter );
	}

	/**
	 * Sets a new or existing query parameter value
	 *
	 * @param string $key   The query parameter to set
	 * @param mixed  $value The value of the query parameter
	 *
	 * @return $this
	 *
	 * @see ReadableWritableProvider::set
	 */
	public function setQueryParameter( string $key, $value ): Request
	{
		$this->queryParameterProvider->set( $key, $value );
		return $this;
	}

	/**
	 * Getter for the passed request body parameter
	 *
	 * If the field is present in the request body, its value is returned as it appears. If not, null is returned.
	 *
	 * @param string $parameter The request body parameter to retrieve
	 *
	 * @return mixed|null
	 *
	 * @see ReadableProvider::getRaw
	 */
	public function getRequestParameter( string $parameter )
	{
		return $this->requestParameterProvider->getRaw( $parameter );
	}

	/**
	 * Sets a new or existing request parameter value
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return $this
	 *
	 * @see ReadableWritableProvider::set
	 */
	public function setRequestParameter( string $key, $value ): Request
	{
		$this->requestParameterProvider->set( $key, $value );
		return $this;
	}

	/**
	 * Getter for the passed cookie name
	 *
	 * If the requested cookie is present, its value is returned. If not, null is returned.
	 *
	 * @param string $name The name of the cookie to retrieve
	 *
	 * @return string|null
	 *
	 * @see ReadableProvider::get
	 */
	public function getCookie( string $name ): ?string
	{
		return $this->cookieProvider->get( $name );
	}
}