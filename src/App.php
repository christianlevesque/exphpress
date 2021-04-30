<?php

namespace Crossview\Exphpress;

use \InvalidArgumentException;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class App
{
	private static App $instance;
	private Router     $router;
	private Request    $request;
	private Response   $response;
	private array      $messages;

	private function __construct( string $domain )
	{
		$this->messages = require __DIR__ . '/config/strings.php';

		if ( !$domain && strcmp( $domain, '' ) !== 0 )
		{
			throw new InvalidArgumentException( sprintf( $this->messages[ 'required_arg' ], 'domain' ) );
		}

		$this->router   = Router::getInstance();
		$this->request  = new Request;
		$this->response = new Response( $domain );
	}

	public function getInstance( string $domain ): App
	{
		if ( !isset( self::$instance ) )
		{
			self::$instance = new self( $domain ?? '' );
		}

		return self::$instance;
	}

	/**
	 * @return Router
	 */
	public function getRouter(): Router
	{
		return $this->router;
	}

	/**
	 * @return Request
	 */
	public function getRequest(): Request
	{
		return $this->request;
	}

	/**
	 * @return Response
	 */
	public function getResponse(): Response
	{
		return $this->response;
	}
}