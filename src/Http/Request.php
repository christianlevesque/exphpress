<?php

namespace Crossview\Exphpress\Http;

use Crossview\Exphpress\Providers\ArrayValueProvider;
use Crossview\Exphpress\Providers\WritableArrayValueProvider;

class Request
{
	/**
	 * @var ArrayValueProvider The $_SERVER ArrayValueProvider
	 */
	private ArrayValueProvider $serverProvider;

	/**
	 * @var ArrayValueProvider The $_COOKIE ArrayValueProvider
	 */
	private ArrayValueProvider $cookieProvider;

	/**
	 * @var WritableArrayValueProvider A provider for URL query parameters
	 */
	private WritableArrayValueProvider $queryParameterProvider;

	/**
	 * @var WritableArrayValueProvider A provider for request body parameters
	 */
	private WritableArrayValueProvider $requestParameterProvider;

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
	 * This is internally backed by the ArrayValueProvider::getRaw method. If the field is present in the URL query parameters, its value is returned as it appears. If not, null is returned.
	 *
	 * @param string $parameter The URI query parameter to retrieve
	 *
	 * @return mixed|null
	 *
	 * @see \Crossview\Exphpress\Providers\ArrayValueProvider::getRaw
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
	 */
	public function setQueryParameter( string $key, $value ): Request
	{
		$this->queryParameterProvider->set( $key, $value );
		return $this;
	}

	/**
	 * Getter for the passed request body parameter
	 *
	 * This is internally backed by the ArrayValueProvider::getRaw method. If the field is present in the request body, its value is returned as it appears. If not, null is returned.
	 *
	 * @param string $parameter The request body parameter to retrieve
	 *
	 * @return mixed|null
	 *
	 * @see \Crossview\Exphpress\Providers\ArrayValueProvider::getRaw
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
	 */
	public function setRequestParameter( string $key, $value ): Request
	{
		$this->requestParameterProvider->set( $key, $value );
		return $this;
	}

	/**
	 * Getter for the passed cookie name
	 *
	 * This method will throw an error if
	 *
	 * This is internally backed by the ArrayValueProvider::get method. If the requested cookie is present, its value is returned. If not, null is returned.
	 *
	 * @param string $name The name of the cookie to retrieve
	 *
	 * @return string|null
	 *
	 * @see \Crossview\Exphpress\Providers\ArrayValueProvider::get
	 */
	public function getCookie( string $name ): ?string
	{
		return $this->cookieProvider->get( $name );
	}

	/**
	 * Gets the registered Server Provider
	 *
	 * @return \Crossview\Exphpress\Providers\ArrayValueProvider|null
	 */
	public function getServerProvider(): ?ArrayValueProvider
	{
		if ( isset( $this->serverProvider ) )
		{
			return $this->serverProvider;
		}

		return null;
	}

	/**
	 * Registers the Server Provider
	 *
	 * This method only sets the Server Provider if it has not already been set. Exphpress-provided middleware calls this method, so there's no reason for developers to call it.
	 *
	 * @param ArrayValueProvider $provider The Server Provider to register
	 *
	 * @return $this
	 */
	public function setServerProvider( ArrayValueProvider $provider ): Request
	{
		if ( !isset( $this->serverProvider ) )
		{
			$this->serverProvider = $provider;
		}

		return $this;
	}

	/**
	 * Gets the Query Parameter Provider
	 *
	 * @return \Crossview\Exphpress\Providers\WritableArrayValueProvider|null
	 */
	public function getQueryParameterProvider(): ?WritableArrayValueProvider
	{
		if ( isset( $this->queryParameterProvider ) )
		{
			return $this->queryParameterProvider;
		}

		return null;
	}

	/**
	 * Registers the Query Parameter Provider
	 *
	 * This method only sets the Query Parameter Provider if it has not already been set. Exphpress-provided middleware calls this method, so there's no reason for developers to call it.
	 *
	 * @param WritableArrayValueProvider $provider The Query Parameter Provider to register
	 *
	 * @return $this
	 */
	public function setQueryParameterProvider( WritableArrayValueProvider $provider ): Request
	{
		if ( !isset( $this->queryParameterProvider ) )
		{
			$this->queryParameterProvider = $provider;
		}

		return $this;
	}

	/**
	 * Gets the Request Parameter Provider
	 *
	 * @return \Crossview\Exphpress\Providers\WritableArrayValueProvider|null
	 */
	public function getRequestParameterProvider(): ?WritableArrayValueProvider
	{
		if ( isset( $this->requestParameterProvider ) )
		{
			return $this->requestParameterProvider;
		}

		return null;
	}

	/**
	 * Registers the Request Parameter Provider
	 *
	 * This method only sets the Request Parameter Provider if it has not already been set. Exphpress-provided middleware calls this method, so there's no reason for developers to call it.
	 *
	 * @param WritableArrayValueProvider $provider The Request Parameter Provider to register
	 *
	 * @return $this
	 */
	public function setRequestParameterProvider( WritableArrayValueProvider $provider ): Request
	{
		if ( !isset( $this->requestParameterProvider ) )
		{
			$this->requestParameterProvider = $provider;
		}

		return $this;
	}

	/**
	 * Gets the Cookie Provider
	 *
	 * @return \Crossview\Exphpress\Providers\ArrayValueProvider|null
	 */
	public function getCookieProvider(): ?ArrayValueProvider
	{
		if ( isset( $this->cookieProvider ) )
		{
			return $this->cookieProvider;
		}

		return null;
	}

	/**
	 * Registers the Cookie Provider
	 *
	 * This method only sets the Cookie Provider if it has not already been set. Exphpress does not provide a default Cookie Provider; a Cookie Provider must be configured here before using cookies in the Request.
	 *
	 * @param ArrayValueProvider $provider The Cookie Provider to register
	 *
	 * @return $this
	 */
	public function setCookieProvider( ArrayValueProvider $provider ): Request
	{
		if ( !isset( $this->cookieProvider ) )
		{
			$this->cookieProvider = $provider;
		}

		return $this;
	}
}