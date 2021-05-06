<?php

namespace Crossview\Exphpress\Http;

use Crossview\Exphpress\Exceptions\ExphpressException;
use Crossview\Exphpress\Providers\CookieProvider;
use Crossview\Exphpress\Providers\HeadersProvider;

class Response
{
	/**
	 * @var int The HTTP Status Code to send along with the payload. Defaults to 200 in the constructor.
	 */
	protected int $responseCode;

	/**
	 * Returns the currently set HTTP Status Code
	 *
	 * @return int Returns the currently set HTTP Status Code
	 */
	public function getResponseCode(): int
	{
		return $this->responseCode;
	}

	/**
	 * Queues a value to be set as the HTTP Status Code
	 *
	 * The HTTP Status Code is not directly set by this method. Instead, the method that sends the response body will also set the HTTP Status Code at that time.
	 *
	 * @param int $code The HTTP Status Code
	 *
	 * @return $this
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
	 * @return $this
	 */
	public function status( int $code ): Response
	{
		return $this->setResponseCode( $code );
	}

	/**
	 * @var string The main body of the payload. Defaults to an empty string in the constructor. The body may be overwritten or appended to, depending on which method is used to interact with the field.
	 */
	protected string $responseBody;

	/**
	 * Gets the currently set HTTP Response Body
	 *
	 * @return string Returns the currently set HTTP Response Body
	 */
	public function getResponseBody(): string
	{
		return $this->responseBody;
	}

	/**
	 * Queues a new value for the response body
	 *
	 * The response body is not directly sent by this method. A different method will send the response body after processing header and cookie queues.
	 *
	 * @param string $body The value to be sent as the response body
	 *
	 * @return $this
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
	 * @return $this
	 */
	public function appendToResponseBody( string $partial ): Response
	{
		$this->responseBody .= $partial;

		return $this;
	}

	/**
	 * @var HeadersProvider A container for HTTP response headers. The headers here will be sent as the payload is sent.
	 */
	protected HeadersProvider $headersProvider;

	/**
	 * Returns the configured HeadersProvider instance
	 *
	 * @return HeadersProvider
	 *
	 * @throws ExphpressException if no HeadersProvider has been configured for the Response
	 */
	public function getHeadersProvider(): HeadersProvider
	{
		if ( !isset( $this->headersProvider ) )
		{
			throw new ExphpressException( 'You are attempting to access the Response HeadersProvider, but none has been configured.' );
		}

		return $this->headersProvider;
	}

	/**
	 * Sets the Headers Provider if it hasn't already been set
	 *
	 * This method is called by Exphpress; develops shouldn't need to call this unless they're executing Exphpress manually.
	 *
	 * @param HeadersProvider $headersProvider
	 *
	 * @return $this
	 *
	 * @throws ExphpressException if the HeadersProvider has already been configured for the Response
	 */
	public function setHeadersProvider( HeadersProvider $headersProvider ): Response
	{
		if ( isset( $this->headersProvider ) )
		{
			throw new ExphpressException( 'You are attempting to set the Response HeadersProvider, but a HeadersProvider has already been configured.' );
		}
		$this->headersProvider = $headersProvider;

		return $this;
	}

	/**
	 * @var CookieProvider
	 */
	protected CookieProvider $cookieProvider;

	/**
	 * Returns the configured CookieProvider instance
	 *
	 * @return CookieProvider
	 *
	 * @throws ExphpressException if no CookieProvider has been configured for the Response
	 */
	public function getCookieProvider(): CookieProvider
	{
		if ( !isset( $this->cookieProvider ) )
		{
			throw new ExphpressException( 'You are attempting to access the Response CookieProvider, but none has been configured.' );
		}

		return $this->cookieProvider;
	}

	/**
	 * Sets the Headers Provider if it hasn't already been set
	 *
	 * This method is called by Exphpress; develops shouldn't need to call this unless they're executing Exphpress manually.
	 *
	 * @param CookieProvider $cookieProvider
	 *
	 * @return $this
	 *
	 * @throws ExphpressException if the HeadersProvider has already been configured for the Response
	 */
	public function setCookieProvider( CookieProvider $cookieProvider ): Response
	{
		if ( isset( $this->cookieProvider ) )
		{
			throw new ExphpressException( 'You are attempting to set the Response CookieProvider, but a CookieProvider has already been configured.' );
		}
		$this->cookieProvider = $cookieProvider;

		return $this;
	}

	/**
	 * Response constructor.
	 */
	public function __construct()
	{
		$this->responseCode = 200;
		$this->responseBody = '';
	}

	/**
	 * Queues a header to be set before sending response body
	 *
	 * The header value is not directly set by this method. Instead, the method that sends the response body will also set the header at that time.
	 *
	 * @param string $name  The name of the header to be set
	 * @param mixed  $value The value of the header to be set
	 *
	 * @return $this
	 */
	public function setHeader( string $name, $value ): Response
	{
		// Use getHeadersProvider to ensure it throws the correct exception if it hasn't been set
		$this->getHeadersProvider()
			 ->setHeader( $name, $value );
		return $this;
	}

	/**
	 * Unqueues a previously queued header
	 *
	 * @param string $name The name of the header to be unset
	 *
	 * @return $this
	 */
	public function unsetHeader( string $name ): Response
	{
		$this->getHeadersProvider()
			 ->unsetHeader( $name );
		return $this;
	}

	/**
	 * Queues a cookie to be set before sending response body
	 *
	 * The cookie value is not directly set by this method. Instead, the method that sends the response body will also set the cookie at that time.
	 *
	 * @param string $name    The name of the cookie to be set
	 * @param mixed  $value   The value of the cookie to be set
	 * @param array  $options (optional) The options for the cookie to be set
	 *
	 * @return $this
	 */
	public function setCookie( string $name, $value, array $options = [] ): Response
	{
		// Use getCookieProvider to ensure it throws the correct exception if it hasn't been set
		$this->getCookieProvider()
			 ->setCookie( $name, $value, $options );
		return $this;
	}

	/**
	 * Unqueues a previously queued cookie
	 *
	 * This method does NOT delete a cookie from a user's browser. This method simply removes a cookie from the cookie queue. To delete a cookie from a user's browser, use Response::deleteCookie.
	 *
	 * @param string $name The name of the cookie to be unset
	 *
	 * @return $this
	 */
	public function unsetCookie( string $name ): Response
	{
		// Use getCookieProvider to ensure it throws the correct exception if it hasn't been set
		$this->getCookieProvider()
			 ->unsetCookie( $name );
		return $this;
	}

	public function deleteCookie( string $name, array $options = [] ): Response
	{
		$this->getCookieProvider()
			 ->deleteCookie( $name, $options );
		return $this;
	}

	/**
	 * Sends the HTTP status code registered on the Response
	 *
	 * Once this has been sent, the HTTP response has started and the status code cannot be changed.
	 *
	 * @return $this
	 */
	public function sendHttpStatus(): Response
	{
		http_response_code( $this->getResponseCode() );
		return $this;
	}

	/**
	 * Sends all HTTP headers registered on the Response
	 *
	 * @return $this
	 */
	public function sendHeaders(): Response
	{
		// Use getHeadersProvider to ensure it throws the correct exception if it hasn't been set
		$this->getHeadersProvider()
			 ->sendHeaders();
		return $this;
	}

	/**
	 * Sends all cookies registered on the Response
	 *
	 * @return $this
	 */
	public function sendCookies(): Response
	{
		// Use getCookieProvider to ensure it throws the correct exception if it hasn't been set
		$this->getCookieProvider()
			 ->sendCookies();
		return $this;
	}

	/**
	 * Sends the HTTP response
	 *
	 * This method sends the entire HTTP response at once. The status code is sent, followed by cookies, followed by other HTTP headers, and finally, the response body, if any.
	 *
	 * @param string $body        The HTTP response body to send in the Response. If already set, this appends to the existing response body unless the $replaceBody argument is set to true
	 * @param false  $replaceBody Whether or not to replace the existing response body with the provided body
	 */
	public function send( string $body = '', bool $replaceBody = false )
	{
		if ( $replaceBody )
		{
			$this->setResponseBody( $body );
		} else if ( !empty( $body ) )
		{
			$this->appendToResponseBody( $body );
		}

		echo $this->sendHttpStatus()
				  ->sendCookies()
				  ->sendHeaders()
				  ->getResponseBody();
	}
}