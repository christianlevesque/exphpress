<?php

namespace Middleware;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\OutputBuffer;
use PHPUnit\Framework\TestCase;

class OutputBufferTest extends TestCase
{
	private OutputBuffer $middleware;
	private Request      $request;
	private Response     $response;

	protected function setUp(): void
	{
		$this->middleware = new OutputBuffer();
		$this->request    = $this->createStub( Request::class );
		$this->response   = $this->createStub( Response::class );
	}

	public function testOutputBufferMiddlewareBuffersOutput(): void
	{
		$this->middleware->handle( $this->request, $this->response, function ()
		{
			echo "output";
		} );
		// ob_get_flush returns false if output buffering isn't active
		$this->assertNotFalse( ob_get_flush() );
	}
}
