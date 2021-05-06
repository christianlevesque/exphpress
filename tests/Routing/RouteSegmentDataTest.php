<?php

namespace Routing;

use Crossview\Exphpress\Routing\RouteSegmentData;
use PHPUnit\Framework\TestCase;

class RouteSegmentDataTest extends TestCase
{
	private RouteSegmentData $data;

	protected function setUp(): void
	{
		$this->data = new RouteSegmentData();
	}

	public function testParamGetterSetter(): void
	{
		$this->assertFalse( $this->data->isParam() );
		$this->data->setParam( true );
		$this->assertTrue( $this->data->isParam() );
	}

	public function testPathGetterSetter(): void
	{
		$this->assertEmpty( $this->data->getPath() );
		$this->data->setPath( '/home' );
		$this->assertEquals( '/home', $this->data->getPath() );
	}

	public function testTypesGetterSetter(): void
	{
		$this->assertIsArray( $this->data->getTypes() );
		$this->assertEmpty( $this->data->getTypes() );
		$this->data->setTypes( [ 'int' ] );
		$this->assertCount( 1, $this->data->getTypes() );
		$this->assertEquals( 'int', $this->data->getTypes()[ 0 ] );
	}

	public function testTypeGetterSetter(): void
	{
		$this->assertEmpty( $this->data->getType() );
		$this->data->setType( 'bool' );
		$this->assertEquals( 'bool', $this->data->getType() );
	}

	public function testValueGetterSetter(): void
	{
		$this->data->setValue( 1 );
		$value = $this->data->getValue();
		$this->assertIsNumeric( $value );
		$this->assertEquals( 1, $value );

		$this->data->setValue( true );
		$value = $this->data->getValue();
		$this->assertIsBool( $value );
		$this->assertTrue( $value );

		$this->data->setValue( 'hello' );
		$value = $this->data->getValue();
		$this->assertIsString( $value );
		$this->assertEquals( 'hello', $value );
	}
}
