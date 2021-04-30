<?php

namespace Crossview\Exphpress\Http;

class Request
{
	/**
	 * @var string The HTTP Request Method of the current request.
	 */
	public string $method;

	/**
	 * @var array The $_COOKIE array as defined by PHP.
	 */
	public array $cookies;

	/**
	 * @var array An array of matched URI parameters, set by the Router class. If no URI parameters were matched, will be empty.
	 */
	public array $parameters;

	/**
	 * @var array An array of request body parameters. If no request body parameters are set, will be empty.
	 */
	public array $requestParameters;

	/**
	 * Request constructor.
	 */
	public function __construct()
	{
		$this->method = $_SERVER[ 'REQUEST_METHOD' ];

		//TODO: set up $requestParameters using parse_str()

		$this->cookies = $_COOKIE;
	}

	/**
	 * Getter for the passed URI parameter
	 *
	 * @param string $parameter The URI parameter to retrieve
	 *
	 * @return mixed|null Returns the value of the URI parameter if it exists; if not, returns null.
	 */
	public function getParameter( string $parameter )
	{
		if ( isset( $this->parameters[ $parameter ] ) )
		{
			return $this->parameters[ $parameter ];
		}

		return null;
	}

	/**
	 * Getter for the passed request body parameter
	 *
	 * @param string $parameter The request body parameter to retrieve
	 *
	 * @return mixed|null Returns the value of the request body parameter if it exists; if not, returns null.
	 */
	public function getRequestParameter( string $parameter )
	{
		if ( isset( $this->requestParameters[ $parameter ] ) )
		{
			return $this->requestParameters[ $parameter ];
		}

		return null;
	}

	/**
	 * Getter for the passed cookie name
	 *
	 * @param string $name The name of the cookie to retrieve
	 *
	 * @return string|null Returns the value of the cookie if it exists; if not, returns null.
	 */
	public function getCookie( string $name ): ?string
	{
		if ( isset( $this->cookies[ $name ] ) )
		{
			return $this->cookies[ $name ];
		}

		return null;
	}
}