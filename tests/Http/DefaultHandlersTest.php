<?php

namespace Http;

use Crossview\Exphpress\Http\DefaultHandlers;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use PHPUnit\Framework\TestCase;

class DefaultHandlersTest extends TestCase
{
	private Request         $request;
	private Response        $response;
	private DefaultHandlers $handlers;

	protected function setUp(): void
	{
		$this->request  = $this->createMock( Request::class );
		$this->response = $this->createMock( Response::class );
		$this->handlers = new DefaultHandlers;
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( DefaultHandlers::class, $this->handlers );
	}

	public function testNotFoundCallsStatus(): void
	{
		$this->response->expects( $this->once() )
					   ->method( 'status' )
					   ->with( 404 );

		$this->handlers->notFound( $this->request, $this->response );
	}
}