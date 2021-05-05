<?php

namespace Crossview\Exphpress\Utilities;

trait CanProcessPaths
{
	/**
	 * Parses a pure root-relative URL (no request parameters) and returns an array
	 *
	 * @param string $path The path to parse
	 *
	 * @return array
	 */
	function processPath( string $path ): array
	{
		$parsed = explode( '/', $path );

		// The first element will be an empty string, what came before the leading slash
		array_shift( $parsed );
		return $parsed;
	}
}