<?php

namespace Middleware;

use Exception;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\ErrorHandler;
use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
{
	private ErrorHandler $handler;
	private Request      $request;
	private Response     $response;

	protected function setUp(): void
	{
		$this->handler  = new ErrorHandler();
		$this->request  = $this->createStub( Request::class );
		$this->response = $this->createMock( Response::class );
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( ErrorHandler::class, $this->handler );
	}

	public function testDoesNothingIfNoErrors(): void
	{
		$expected = "This is my output. It's such a nice output.";

		$this->response->expects( $this->never() )
					   ->method( 'status' );
		$this->response->expects( $this->never() )
					   ->method( 'send' );

		ob_start();
		$this->handler->handle( $this->request, $this->response, function () use ( $expected )
		{
			echo $expected;
		} );
		$output = ob_get_clean();
		$this->assertEquals( $expected, $output );
	}

	public function testHandlesAllErrors(): void
	{
		$this->response->expects( $this->once() )
					   ->method( 'status' )
					   ->with( 500 )
					   ->willReturn( $this->response );

		$this->handler->handle( $this->request, $this->response, function ()
		{
			throw new Exception( 'This is an exception', 500 );
		} );
	}
}
