<?php

namespace Crossview\Exphpress;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;

class App
{
	private Router     $router;
	private Request    $request;
	private Response   $response;
	private array      $messages;

	public function __construct( string $domain )
	{
		$this->messages = require __DIR__ . '/config/strings.php';

		if ( !$domain && strcmp( $domain, '' ) !== '' )
		{
			throw new \InvalidArgumentException( sprintf( $this->messages[ 'required_arg' ], 'domain' ) );
		}

		$this->router   = new Router;
		$this->request  = new Request;
		$this->response = new Response( $domain );
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