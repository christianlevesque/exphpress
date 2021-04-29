<?php

namespace Crossview\Exphpress;

use Exphpress\Http\Request;
use Exphpress\Http\Response;

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

		if ( !$domain && strcmp( $domain, '' ) !== '' )
		{
			throw new \InvalidArgumentException( sprintf( $this->messages[ 'required_arg' ], 'domain' ) );
		}

		$this->router   = new Router;
		$this->request  = new Request;
		$this->response = new Response( $domain );
	}

	public static function getInstance()
	{
		if ( !isset( self::$instance ) )
		{
			self::$instance = new self;
		}

		return self::$instance;
	}
}