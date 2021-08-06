<?php

namespace Crossview\Exphpress;

use Crossview\Exphpress\Http\Handlers;
use Crossview\Exphpress\Http\DefaultHandlers;
use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Http\Response;
use Crossview\Exphpress\Middleware\ErrorHandler;
use Crossview\Exphpress\Middleware\Middleware;
use Crossview\Exphpress\Middleware\MiddlewareContainer;
use Crossview\Exphpress\Middleware\OutputBuffer;
use Crossview\Exphpress\Middleware\RequestConfigurer;
use Crossview\Exphpress\Middleware\ResponseConfigurer;
use Crossview\Exphpress\Middleware\RouteHandlerMiddleware;
use Crossview\Exphpress\Routing\Router;

class App
{
	protected static App $instance;

	/**
	 * @return $this
	 */
	public static function getInstance(): self
	{
		if ( !isset( self::$instance ) )
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * @var MiddlewareContainer|null Middleware container
	 */
	protected ?MiddlewareContainer $middlewareContainer = null;

	/**
	 * Gets the App's MiddlewareContainer instance
	 *
	 * @return MiddlewareContainer|null
	 */
	public function getMiddlewareContainer(): ?MiddlewareContainer
	{
		return $this->middlewareContainer;
	}

	/**
	 * Sets the App's MiddlewareContainer instance
	 *
	 * @param MiddlewareContainer $container The MiddlewareContainer instance to save to the App
	 *
	 * @return $this
	 */
	public function setMiddlewareContainer( MiddlewareContainer $container ): self
	{
		$this->middlewareContainer = $container;
		return $this;
	}

	/**
	 * @var Request|null The App HTTP Request
	 */
	protected ?Request $request = null;

	/**
	 * Gets the App's Request instance
	 *
	 * @return Request|null
	 */
	public function getRequest(): ?Request
	{
		return $this->request;
	}

	/**
	 * Sets the App's Request instance
	 *
	 * @param Request $request The Request instance to save to the App
	 *
	 * @return $this
	 */
	public function setRequest( Request $request ): self
	{
		$this->request = $request;
		return $this;
	}

	/**
	 * @var Response|null The App HTTP Response
	 */
	protected ?Response $response = null;

	/**
	 * Gets the App's Response instance
	 *
	 * @return Response|null
	 */
	public function getResponse(): ?Response
	{
		return $this->response;
	}

	/**
	 * Sets the App's Response instance
	 *
	 * @param Response $response The Response instance to save to the App
	 *
	 * @return $this
	 */
	public function setResponse( Response $response ): self
	{
		$this->response = $response;
		return $this;
	}

	/**
	 * @var Router|null The app URL router
	 */
	protected ?Router $router = null;

	/**
	 * Gets the App's Router instance
	 *
	 * @return Router|null
	 */
	public function getRouter(): ?Router
	{
		return $this->router;
	}

	/**
	 * Sets the App's Router instance
	 *
	 * @param Router $router The Router instance to save to the App
	 *
	 * @return $this
	 */
	public function setRouter( Router $router ): self
	{
		$this->router = $router;
		return $this;
	}

	/**
	 * @var Middleware|null The App output buffering middleware
	 */
	protected ?Middleware $outputBuffer = null;

	/**
	 * Gets the App's output buffering middleware
	 *
	 * @return Middleware|null
	 */
	public function getOutputBuffer(): ?Middleware
	{
		return $this->outputBuffer;
	}

	/**
	 * Sets the App's output buffering middleware
	 *
	 * @param Middleware $outputBuffer The output buffering Middleware to save to the App
	 *
	 * @return $this
	 */
	public function setOutputBuffer( Middleware $outputBuffer ): self
	{
		$this->outputBuffer = $outputBuffer;
		return $this;
	}

	/**
	 * @var Middleware|null The App error handling middleware
	 */
	protected ?Middleware $errorHandler = null;

	/**
	 * Gets the App's error handling middleware
	 *
	 * @return Middleware|null
	 */
	public function getErrorHandler(): ?Middleware
	{
		return $this->errorHandler;
	}

	/**
	 * Sets the App's error handling middleware
	 *
	 * @param Middleware $errorHandler The error handling Middleware to save to the App
	 *
	 * @return $this
	 */
	public function setErrorHandler( Middleware $errorHandler ): self
	{
		$this->errorHandler = $errorHandler;
		return $this;
	}

	/**
	 * @var Middleware|null The App request configurer
	 */
	protected ?Middleware $requestConfigurer = null;

	/**
	 * Gets the App request configurer
	 *
	 * @return Middleware|null
	 */
	public function getRequestConfigurer(): ?Middleware
	{
		return $this->requestConfigurer;
	}

	/**
	 * Sets the App request configurer
	 *
	 * @param Middleware|null $requestConfigurer
	 *
	 * @return $this
	 */
	public function setRequestConfigurer( ?Middleware $requestConfigurer ): self
	{
		$this->requestConfigurer = $requestConfigurer;
		return $this;
	}

	/**
	 * @var Middleware|null The App response configurer
	 */
	protected ?Middleware $responseConfigurer = null;

	/**
	 * @return Middleware|null Gets the App response configurer
	 */
	public function getResponseConfigurer(): ?Middleware
	{
		return $this->responseConfigurer;
	}

	/**
	 * Sets the App response configurer
	 *
	 * @param Middleware|null $responseConfigurer
	 *
	 * @return $this
	 */
	public function setResponseConfigurer( ?Middleware $responseConfigurer ): self
	{
		$this->responseConfigurer = $responseConfigurer;
		return $this;
	}

	/**
	 * @var Middleware|null The App route handler
	 */
	protected ?Middleware $routeHandler = null;

	/**
	 * Gets the App route handler
	 *
	 * @return Middleware|null
	 */
	public function getRouteHandler(): ?Middleware
	{
		return $this->routeHandler;
	}

	/**
	 * Sets the App route handler
	 *
	 * @param Middleware|null $routeHandler
	 *
	 * @return $this
	 */
	public function setRouteHandler( ?Middleware $routeHandler ): self
	{
		$this->routeHandler = $routeHandler;
		return $this;
	}

	/**
	 * @var Handlers|null The App handlers factory
	 */
	protected ?Handlers $handlers = null;

	/**
	 * Gets the App handlers factory
	 *
	 * @return Handlers|null
	 */
	public function getHandlers(): ?Handlers
	{
		return $this->handlers;
	}

	/**
	 * @param Handlers|null $handlers
	 *
	 * @return $this
	 */
	public function setHandlers( ?Handlers $handlers ): self
	{
		$this->handlers = $handlers;
		return $this;
	}

	protected function __construct()
	{
	}

	/**
	 * Configures the application instances and registers the default middleware
	 *
	 * App::configure() first checks if certain defaults are already set on the App before instantiating them, allowing developers to pass their own versions of those defaults. To override these defaults, call the appropriate setter method on App.
	 *
	 * @return $this
	 */
	public function configure(): self
	{
		// Router
		$this->router ??= Router::getInstance();

		// MiddlewareContainer
		$this->request             ??= new Request;
		$this->response            ??= new Response;
		$this->middlewareContainer ??= new MiddlewareContainer( $this->request, $this->response );

		// Middleware
		$this->outputBuffer       ??= new OutputBuffer;
		$this->errorHandler       ??= new ErrorHandler;
		$this->requestConfigurer  ??= new RequestConfigurer;
		$this->responseConfigurer ??= new ResponseConfigurer;
		$this->routeHandler       ??= new RouteHandlerMiddleware;
		$this->handlers           ??= new DefaultHandlers;

		// Set up middleware
		return $this->register( $this->outputBuffer )
					->register( $this->errorHandler )
					->register( $this->requestConfigurer )
					->register( $this->responseConfigurer );
	}

	/**
	 * Registers a middleware on the application
	 *
	 * @param Middleware $middleware The middleware to register
	 *
	 * @return $this
	 */
	public function register( Middleware $middleware ): App
	{
		$this->middlewareContainer->register( $middleware );

		return $this;
	}

	/**
	 * Executes the middleware registered on the application
	 */
	public function execute(): void
	{
		$this->middlewareContainer->register( $this->routeHandler );
		$this->middlewareContainer->execute();
	}
}