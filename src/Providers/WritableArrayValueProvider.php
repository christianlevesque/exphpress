<?php

namespace Crossview\Exphpress\Providers;

/**
 * Provides a writable version of ArrayValueProvider
 *
 * To write to the value array, call WritableArrayValueProvider::set with the key and value to set.
 *
 * @package Crossview\Exphpress\Providers
 */
class WritableArrayValueProvider extends ArrayValueProvider implements ReadableWritableProvider
{
	public function set( $key, $value ): WritableArrayValueProvider
	{
		$this->values[ $key ] = $value;
		return $this;
	}
}