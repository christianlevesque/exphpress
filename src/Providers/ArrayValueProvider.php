<?php


namespace Crossview\Exphpress\Providers;


class ArrayValueProvider
{
	protected array $values;

	public function __construct( array $input )
	{
		$this->values = $input;
	}

	/**
	 * Gets a value from the backing array, or null if index doesn't exist
	 *
	 * ArrayValueProvider::get casts the value to a string before returning. If you desire the original datatype to be preserved, use ArrayValueProvider::getRaw.
	 *
	 * @param string $index The index of the value to retrieve
	 *
	 * @return string|null
	 */
	public function get( string $index ): ?string
	{
		if ( array_key_exists( $index, $this->values ) )
		{
			return (string) $this->values[ $index ];
		}

		return null;
	}

	/**
	 * Gets a value from the backing array with its original datatype preserved, or null if index doesn't exist
	 *
	 * @param string $index The index of the value to retrieve
	 *
	 * @return mixed|null
	 */
	public function getRaw( string $index )
	{
		if ( array_key_exists( $index, $this->values ) )
		{
			return $this->values[ $index ];
		}

		return null;
	}
}