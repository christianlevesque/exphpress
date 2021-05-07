<?php

namespace Middleware;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\ResponseConfigurer;
use Crossview\Exphpress\Providers\CookieProvider;
use Crossview\Exphpress\Providers\HeadersProvider;
use PHPUnit\Framework\TestCase;

class ResponseConfigurerTest extends TestCase
{
	private ResponseConfigurer $middleware;
	private Request            $request;
	private Response           $response;

	protected function setUp(): void
	{
		$this->middleware = new ResponseConfigurer();
		$this->request    = $this->createStub( Request::class );
		$this->response   = $this->createMock( Response::class );
	}

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( ResponseConfigurer::class, $this->middleware );
	}

	public function testCallsConfigureProviders(): void
	{
		$this->response->expects($this->once())
			->method('setHeadersProvider')
			->with($this->isInstanceOf(HeadersProvider::class));
		$this->response->expects($this->once())
			->method('setCookieProvider')
			->with($this->isInstanceOf(CookieProvider::class));

		$this->middleware->handle($this->request, $this->response, function() {
		});
	}
}
