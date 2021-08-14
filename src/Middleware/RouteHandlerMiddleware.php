<?php

namespace Crossview\Exphpress\Middleware;

use Closure;
use Crossview\Exphpress\App;
use Crossview\Exphpress\Http\Handlers;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Routing\Router;
use Crossview\Exphpress\Utilities\RouteProcessor;

class RouteHandlerMiddleware implements Middleware
{
	protected RouteProcessor $processor;
	protected Router         $router;
	protected Handlers       $handlers;

	public function __construct( ?App $app = null, ?RouteProcessor $processor = null )
	{
		$app       ??= App::getInstance();
		$processor ??= new RouteProcessor();

		$this->processor = $processor;
		$this->router    = $app->getRouter();
		$this->handlers  = $app->getHandlers();
	}

	/**
	 * @inheritDoc
	 */
	public function handle( Request $request, Response $response, Closure $next )
	{
		foreach ( $this->router->getRoutes() as $route ) {
			if ( $this->processor->routeMatches( $route, $request ) ) {
				$route->execute( $request, $response );
				return;
			}
		}

		$this->handlers->notFound( $request, $response );
	}
}