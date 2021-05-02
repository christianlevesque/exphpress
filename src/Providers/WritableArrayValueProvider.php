<?php


namespace Crossview\Exphpress\Providers;

/**
 * Provides a writable version of ArrayValueProvider
 *
 * To write to the value array, call WritableArrayValueProvider::set with the key and value to set.
 *
 * @package Crossview\Exphpress\Providers
 */
class WritableArrayValueProvider extends ArrayValueProvider
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
	public function set( $key, $value ): WritableArrayValueProvider
	{
		$this->values[ $key ] = $value;
		return $this;
	}
}