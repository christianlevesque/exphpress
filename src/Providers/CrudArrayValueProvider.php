<?php


namespace Crossview\Exphpress\Providers;


class CrudArrayValueProvider extends WritableArrayValueProvider implements CrudProvider
{
	function unset( string $key ): CrudArrayValueProvider
	{
		unset( $this->values[ $key ] );
		return $this;
	}

	function unsetAll(): CrudArrayValueProvider
	{
		$this->values = [];
		return $this;
	}
}