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
}
