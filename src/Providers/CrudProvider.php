<?php


namespace Crossview\Exphpress\Providers;


interface CrudProvider extends ReadableWritableProvider
{
	/**
	 * Unsets a value by key if it exists.
	 *
	 * @param string $key The key to unset
	 *
	 * @return $this
	 */
	function unset( string $key ): CrudProvider;

	/**
	 * Unsets all values
	 *
	 * @return $this
	 */
	function unsetAll(): CrudProvider;
}