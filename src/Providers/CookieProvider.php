<?php


namespace Crossview\Exphpress\Providers;


class CookieProvider
{
	private CrudArrayValueProvider $cookies;
	/**
	 * @var array The default options to use when setting a cookie
	 */
	private array $cookieOptions;

	/**
	 * Gets the default cookie options
	 *
	 * @return array
	 */
	public function getCookieOptions(): array
	{
		return $this->cookieOptions;
	}

	public function __construct( array $overrideDefaultOptions = [], array $input = [] )
	{
		$this->cookies       = new CrudArrayValueProvider( $input );
		$defaultOptions      = [
			'expires'  => time() + 60 * 60 * 24,
			'path'     => '/',
			'domain'   => '',
			'secure'   => true,
			'httponly' => true,
			'samesite' => 'lax'
		];
		$this->cookieOptions = array_merge( $defaultOptions, $overrideDefaultOptions );
	}

	public function getCookie( string $name ): ?array
	{
		return $this->cookies->getRaw( $name );
	}

	/**
	 * Queues a cookie to be created
	 *
	 * @param string $name    The name of the cookie to queue
	 * @param string $value   The value of the cookie to queue
	 * @param array  $options The cookie options to queue
	 *
	 * @return $this
	 */
	public function setCookie( string $name, string $value, array $options = [] ): CookieProvider
	{
		$cookieOptions = array_merge( $this->cookieOptions, $options );
		$this->cookies->set( $name, [
			'value'   => $value,
			'options' => $cookieOptions
		] );

		return $this;
	}

	/**
	 * Unqueues a cookie to be created
	 *
	 * This method is not suitable for deleting existing cookies; it merely unqueues a cookie that has not yet been sent. To delete an existing cookie, use CookieProvider::deleteCookie.
	 *
	 * @param string $name The cookie to unset
	 *
	 * @return $this
	 */
	public function unsetCookie( string $name ): CookieProvider
	{
		$this->cookies->unset( $name );
		return $this;
	}

	/**
	 * Queues a cookie to be deleted
	 *
	 * This method is suitable for deleting existing cookies. It queues a new cookie with an empty value and an 'expires' timestamp of 1, effectively overwriting the existing cookie in the browser and then immediately deleting the stale cookie.
	 *
	 * In order to delete a cookie in this way, you MUST provide the same options used when creating the cookie. These options will still be merged with the default options for the CookieProvider, so you may still skip those options you did not explicitly set.
	 *
	 * @param string $name
	 * @param array  $options
	 *
	 * @return $this
	 */
	public function deleteCookie( string $name, array $options = [] ): CookieProvider
	{
		$deleteOptions = [ 'expires' => 1 ];
		$parsedOptions = array_merge( $deleteOptions, $options );
		$this->setCookie( $name, '', $parsedOptions );

		return $this;
	}

	/**
	 * Sends cookies in the queue
	 */
	public function sendCookies()
	{
		$cookieKeys = array_keys( $this->cookies->getAll() );

		for ( $i = 0; $i < count( $cookieKeys ); $i++ )
		{
			$name   = $cookieKeys[ $i ];
			$cookie = $this->getCookie( $name );
			setcookie( $name, $cookie[ 'value' ], $cookie[ 'options' ] );
		}

		$this->cookies->unsetAll();
	}
}