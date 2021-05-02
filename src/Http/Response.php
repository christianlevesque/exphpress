<?php

namespace Crossview\Exphpress\Http;

class Response
{
	/**
	 * @var int The HTTP Status Code to send along with the payload. Defaults to 200 in the constructor.
	 */
	private int $responseCode;

	/**
	 * @var string The main body of the payload. Defaults to an empty string in the constructor. The body may be overwritten or appended to, depending on which method is used to interact with the field.
	 */
	private string $responseBody;

	/**
	 * @var array A list of headers to be sent with the payload, stored as key => value pairs. The headers here will be processed as the payload is sent.
	 */
	private array $headers;

	/**
	 * @var array A list of cookies to be sent with the payload, stored as key => value pairs. The cookies here will be processed as the payload is sent.
	 */
	private $cookies;

	/**
	 * @var array The default cookie options, used by Response::setCookie() and Response::unsetCookie(). Can be overriden by passing an associative array of options to either method.
	 */
	private $cookieOptions;

	/**
	 * Returns the currently set HTTP Status Code
	 *
	 * @return int Returns the currently set HTTP Status Code
	 */
	public function getResponseCode()
	{
		return $this->responseCode;
	}

	/**
	 * Gets the currently set HTTP Response Body
	 *
	 * @return string Returns the currently set HTTP Response Body
	 */
	public function getResponseBody()
	{
		return $this->responseBody;
	}

	/**
	 * Gets the currently queued HTTP Response Headers
	 *
	 * @return array Returns the currently queued HTTP Response Headers
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Gets the currently queued cookies
	 *
	 * @return array Returns the currently queued cookies
	 */
	public function getCookies()
	{
		return $this->cookies;
	}

	/**
	 * Response constructor.
	 */
	public function __construct()
	{
		// TODO: Allow configuration of the defaults found here
		$this->responseCode  = 200;
		$this->responseBody  = "";
		$this->headers       = [];
		$this->cookies       = [];
		$this->cookieOptions = [
			'expires'  => time() + 60 * 60 * 24,
			'path'     => '/',
			'domain'   => '',
			'secure'   => true,
			'httponly' => true,
			'samesite' => 'lax'
		];
	}

	/**
	 * Queues a value to be set as the HTTP Status Code
	 *
	 * The HTTP Status Code is not directly set by this method. Instead, the method that sends the response body will also set the HTTP Status Code at that time.
	 *
	 * @param int $code The HTTP Status Code
	 *
	 * @return $this Returns the instance of the Response object (to enable method chaining)
	 */
	public function setResponseCode( int $code ): Response
	{
		$this->responseCode = $code;

		return $this;
	}

	/**
	 * An alias for setResponseCode
	 *
	 * The status() method is an alias for the setResponseCode(), as status() is the method expected by users of the Express.js library
	 *
	 * @param int $code The HTTP Status Code
	 *
	 * @return Response
	 */
	public function status( int $code ): Response
	{
		return $this->setResponseCode( $code );
	}

	/**
	 * Queues a new value for the response body
	 *
	 * The response body is not directly sent by this method. A different method will send the response body after processing header and cookie queues.
	 *
	 * @param string $body The value to be sent as the response body
	 *
	 * @return Response
	 */
	public function setResponseBody( string $body ): Response
	{
		$this->responseBody = $body;

		return $this;
	}

	/**
	 * Appends an additiional value for the response body
	 *
	 * The response body is not directly sent by this method. A different method will send the response body after processing header and cookie queues.
	 *
	 * @param string $partial The value to be appended to the existing response body
	 *
	 * @return Response
	 */
	public function appendToResponseBody( string $partial ): Response
	{
		$this->responseBody .= $partial;

		return $this;
	}

	/**
	 * Queues a header to be set before sending response body
	 *
	 * The header value is not directly set by this method. Instead, the method that sends the response body will also set the header at that time.
	 *
	 * @param string $name The name of the header to be set
	 * @param mixed $value The value of the header to be set
	 *
	 * @return Response Returns the instance of the Response object (to enable method chaining)
	 */
	public function setHeader( string $name, $value ): Response
	{
		$this->headers[ $name ] = $value;

		return $this;
	}

	/**
	 * Queues a cookie to be set before sending response body
	 *
	 * The cookie value is not directly set by this method. Instead, the method that sends the response body will also set the cookie at that time.
	 *
	 * @param string $name The name of the cookie to be set
	 * @param mixed $value The value of the cookie to be set
	 * @param array $options (optional) The options for the cookie to be set
	 *
	 * @return Response
	 */
	public function setCookie( string $name, $value, $options = null ): Response
	{
		$parsedOptions          = $options
			? array_merge( $this->cookieOptions, $options )
			: $this->cookieOptions;
		$this->cookies[ $name ] = [
			'value'   => $value,
			'options' => $parsedOptions
		];

		return $this;
	}

	/**
	 * Queues a cookie to be unset before sending response body
	 *
	 * This method is merely a wrapper around Response::setCookie() (because PHP cookies are set and unset in the same way). It merges the standard default options with user-defined options, and also merges with an array that sets the 'expires' value to 1 (this is a Unix timestamp, so 1 refers to 1 Jan 1970 at 00:00:01)
	 *
	 * @param string $name The name of the cookie to be deleted
	 * @param array $options (optional) The options for the cookie to be deleted. These must be the same as the options used to set the cookie, or else the cookie will not be deleted.
	 *
	 * @return Response
	 */
	public function unsetCookie( string $name, $options = null ): Response
	{
		$expiredTime   = [ 'expires' => 1 ];
		$deleteOptions = $options
			? array_merge( $this->cookieOptions, $options, $expiredTime )
			: array_merge( $this->cookieOptions, $expiredTime );
		$this->setCookie( $name, '', $deleteOptions );

		return $this;
	}

	public function sendHttpStatus()
	{
		http_response_code( $this->getResponseCode() );
	}

	public function sendHeaders()
	{
		foreach ( $this->getHeaders() as $name => $value )
		{
			header( "$name: $value" );
		}
	}

	public function sendCookies()
	{
		foreach ( $this->getCookies() as $name => $cookie )
		{
			setcookie( $name, $cookie[ 'value' ], $cookie[ 'options' ] );
		}
	}

	public function send( $body = '', $replaceBody = false )
	{
		if ( $replaceBody )
		{
			$this->setResponseBody( $body );
		} else
		{
			if ( !empty( $body ) )
			{
				$this->appendToResponseBody( $body );
			}
		}

		$this->sendHttpStatus();
		$this->sendCookies();
		$this->sendHeaders();
		echo $this->getResponseBody();
	}
}