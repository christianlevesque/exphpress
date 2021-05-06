<?php


namespace Crossview\Exphpress\Routing;


class RouteSegmentData
{
	/**
	 * @var bool Whether the current Route segment is a dynamic parameter or static route segment
	 */
	protected bool $param = false;

	/**
	 * Getter for $param
	 *
	 * @return bool
	 */
	public function isParam(): bool
	{
		return $this->param;
	}

	/**
	 * Setter for $param
	 *
	 * @param bool $param
	 */
	public function setParam( bool $param ): void
	{
		$this->param = $param;
	}

	/**
	 * @var string The value of the current segment (either the static route value or the name of the resolved dynamic route value)
	 */
	protected string $path = '';

	/**
	 * Getter for $path
	 *
	 * @return string
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * Setter for $path
	 *
	 * @param string $path
	 */
	public function setPath( string $path ): void
	{
		$this->path = $path;
	}

	/**
	 * @var array The acceptable data types for the current dynamic route segment (empty if segment is not dynamic)
	 */
	protected array $types = [];

	/**
	 * Getter for $types
	 *
	 * @return array
	 */
	public function getTypes(): array
	{
		return $this->types;
	}

	/**
	 * Setter for $types
	 *
	 * @param array $types
	 */
	public function setTypes( array $types ): void
	{
		$this->types = $types;
	}
}