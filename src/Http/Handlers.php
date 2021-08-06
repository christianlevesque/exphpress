<?php

namespace Crossview\Exphpress\Http;

interface Handlers
{
	function notFound(Request $request, Response $response): void;
}