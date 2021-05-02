<?php

namespace Providers;

use Crossview\Exphpress\Providers\WritableArrayValueProvider;
use PHPUnit\Framework\TestCase;

class WritableArrayValueProviderTest extends TestCase
{
	public function testSetSetsTheProvidedValue(): void
	{
		$provider = new WritableArrayValueProvider( [] );
		$key      = 'test_val';
		$value    = 42;
		$provider->set( $key, $value );

		$this->assertEquals( $value, $provider->getRaw( $key ) );
	}
}
