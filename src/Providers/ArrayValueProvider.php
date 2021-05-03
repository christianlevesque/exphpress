<?php

namespace Crossview\Exphpress\Providers;

class ArrayValueProvider implements ReadableProvider
{
	protected array $values;

	public function __construct( array $input )
	{
		$this->values = $input;
	}

	/**
	 * @inheritDoc
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
	 * @inheritDoc
	 */
	public function getAll(): array
	{
		return $this->values;
	}

	/**
	 * @inheritDoc
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