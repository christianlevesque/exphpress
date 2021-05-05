<?php

namespace Utilities;

use Crossview\Exphpress\Utilities\RouteMatcher;
use PHPUnit\Framework\TestCase;

class RouteMatcherTest extends TestCase
{
	private RouteMatcher $matcher;

	protected function setUp(): void
	{
		$this->matcher = new RouteMatcher;
	}

	// generateUrlDataMap
	public function testGenerateUrlDataMapDoesNothingIfUrlEmpty(): void
	{
		$parsedRoute = $this->matcher->generateUrlDataMap( [] );
		$this->assertEmpty( $parsedRoute );
	}

	public function testGenerateUrlDataMapCorrectlyHandlesParameters(): void
	{
		$parsedRoute = $this->matcher->generateUrlDataMap( [ ':some_value' ] );
		$segment     = $parsedRoute[ 0 ];

		$this->assertArrayHasKey( 'param', $segment );
		$this->assertArrayHasKey( 'path', $segment );
		$this->assertArrayHasKey( 'type', $segment );
		$this->assertTrue( $segment[ 'param' ] );
		$this->assertEquals( 'some_value', $segment[ 'path' ] );
		$this->assertIsArray( $segment[ 'type' ] );
		$this->assertCount( 1, $segment[ 'type' ] );
		$this->assertEquals( 'any', $segment[ 'type' ][ 0 ] );
	}

	public function testGenerateUrlDataMapCorrectlyHandlesParametersWithTypes(): void
	{
		$parsedRoute = $this->matcher->generateUrlDataMap( [ ':some_value<int|bool>' ] );
		$segment     = $parsedRoute[ 0 ];

		$this->assertArrayHasKey( 'param', $segment );
		$this->assertArrayHasKey( 'path', $segment );
		$this->assertArrayHasKey( 'type', $segment );
		$this->assertTrue( $segment[ 'param' ] );
		$this->assertEquals( 'some_value', $segment[ 'path' ] );
		$this->assertIsArray( $segment[ 'type' ] );
		$this->assertCount( 2, $segment[ 'type' ] );
		$this->assertEquals( 'int', $segment[ 'type' ][ 0 ] );
		$this->assertEquals( 'bool', $segment[ 'type' ][ 1 ] );
	}

	public function testGenerateUrlDataMapCorrectlyHandlesRegularRoute(): void
	{
		$parsedRoute = $this->matcher->generateUrlDataMap( [ 'home' ] );
		$segment     = $parsedRoute[ 0 ];

		$this->assertArrayHasKey( 'param', $segment );
		$this->assertArrayHasKey( 'path', $segment );
		$this->assertFalse( $segment[ 'param' ] );
		$this->assertEquals( 'home', $segment[ 'path' ] );
	}

	// validateUrlParameterTypes
	public function testValidateUrlParameterTypesReturnsTypesIfValid(): void
	{
		$types = $this->matcher->validateUrlParameterTypes( [
			'int',
			'float',
			'double',
			'bool'
		] );
		$this->assertCount( 4, $types );
		$this->assertEquals( 'int', $types[ 0 ] );
		$this->assertEquals( 'float', $types[ 1 ] );
		$this->assertEquals( 'double', $types[ 2 ] );
		$this->assertEquals( 'bool', $types[ 3 ] );
	}

	public function testValidateUrlParameterTypesThrowsIfTypeInvalid(): void
	{
		$this->expectErrorMessage( 'string is not a valid parameter data type. Valid types are bool, float, double, int' );
		$this->matcher->validateUrlParameterTypes( [
			'int',
			'string'
		] );
	}

	// parseUrlParameterTypes
	public function testParseUrlParameterTypesReturnsArrayOfTypesIfValid(): void
	{
		$types = $this->matcher->parseUrlParameterTypes( ':matched_route<int|float|double|bool>' );
		$this->assertCount( 4, $types );
	}

	public function testParseUrlParameterTypesReturnsAnyIfNoMatchFound(): void
	{
		$types = $this->matcher->parseUrlParameterTypes( ':matched_route' );
		$this->assertCount( 1, $types );
		$this->assertEquals( 'any', $types[ 0 ] );
	}

	public function testParseUrlParameterTypesReturnsAnyIfParsingFails(): void
	{
		$types = $this->matcher->parseUrlParameterTypes( ':matched_route<int|bool' );
		$this->assertCount( 1, $types );
		$this->assertEquals( 'any', $types[ 0 ] );
	}

	// parseUrlparameterName
	public function testParseUrlParameterNameReturnsNameIfValid(): void
	{
		$this->assertEquals( 'match_name', $this->matcher->parseUrlParameterName( ':match_name<int|bool>' ) );
		$this->assertEquals( 'match_name', $this->matcher->parseUrlParameterName( ':match_name<int>' ) );
		$this->assertEquals( 'match_name', $this->matcher->parseUrlParameterName( ':match_name' ) );
	}

	public function testParseUrlParameterNameThrowsIfNoColon(): void
	{
		$this->expectErrorMessage( 'Route parameter match_name must start with a leading colon (:)' );
		$this->matcher->parseUrlParameterName( 'match_name' );
	}

	public function testParseUrlParameterNameThrowsIfMatchEmpty(): void
	{
		$this->expectErrorMessage( 'Parameter name cannot be empty' );
		// Invalid character
		$this->matcher->parseUrlParameterName( ':<int|bool>' );
	}

	// isUrlParameter
	public function testIsUrlParameterReturnsTrueIfUrlParameter(): void
	{
		$this->assertTrue( $this->matcher->isUrlParameter( ':routeparam' ) );
	}

	public function testIsUrlParameterReturnsTrueIfUrlParameterIncludesTypeData(): void
	{
		$this->assertTrue( $this->matcher->isUrlParameter( ':routeparam<type1|type2>' ) );
	}

	public function testIsUrlParameterReturnsFalseIfStringNotUrlParameter(): void
	{
		$this->assertFalse( $this->matcher->isUrlParameter( 'routeparam' ) );
	}

}
