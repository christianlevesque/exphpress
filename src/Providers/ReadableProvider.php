<?php

namespace Crossview\Exphpress\Providers;

interface ReadableProvider
{
	/**
	 * Gets a value from the backing array, or null if index doesn't exist
	 *
	 * ArrayValueProvider::get casts the value to a string before returning. If you desire the original datatype to be preserved, use ArrayValueProvider::getRaw.
	 *
	 * @param string $index The index of the value to retrieve
	 *
	 * @return string|null
	 */
	function get( string $index ): ?string;

	/**
	 * Gets a value from the backing array with its original datatype preserved, or null if index doesn't exist
	 *
	 * @param string $index The index of the value to retrieve
	 *
	 * @return mixed|null
	 */
	function getRaw( string $index );
}