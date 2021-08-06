<?php

namespace Crossview\Exphpress\Http;

class DefaultHandlers implements Handlers
{
	public function notFound(Request $request, Response $response): void
	{
		$response->status(404);
	}
}