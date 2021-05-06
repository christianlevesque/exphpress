<?php

namespace Crossview\Exphpress\Providers;

interface WritableProvider
{
	/**
	 * Sets arbitrary key => value pairs in the backing array
	 *
	 * This method provides a fluent API.
	 *
	 * @param string|int $key   The key to create in the array
	 * @param mixed      $value The value to set in the array
	 *
	 * @return $this
	 */
	function set( string $key, $value ): WritableProvider;
}