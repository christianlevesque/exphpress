<?php

namespace Utilities;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Routing\Route;
use Crossview\Exphpress\Utilities\RouteProcessor;
use PHPUnit\Framework\TestCase;

class RouteProcessorTest extends TestCase
{
	private RouteProcessor $matcher;

	protected function setUp(): void
	{
		$this->matcher = new RouteProcessor;
	}

	// routeMatches
	public function testRouteMatchesReturnsFalseIfUrlLengthTooShort(): void
	{
		$route   = new Route( '/no/place/like/home' );
		$request = new Request();
		$request->setPath( '/home' );

		$this->assertFalse( $this->matcher->routeMatches( $route, $request ) );
	}

	public function testRouteMatchesReturnsFalseIfUrlLengthTooLong(): void
	{
		$route   = new Route( '/home' );
		$request = new Request();
		$request->setPath( '/no/place/like/home' );

		$this->assertFalse( $this->matcher->routeMatches( $route, $request ) );
	}

	public function testRouteMatchesReturnsTrueIfSimpleRouteMatches(): void
	{
		$path    = '/home';
		$route   = new Route( $path );
		$request = new Request();
		$request->setPath( $path );
		$this->assertTrue( $this->matcher->routeMatches( $route, $request ) );

		$path    = '/';
		$route   = new Route( $path );
		$request = new Request();
		$request->setPath( $path );
		$this->assertTrue( $this->matcher->routeMatches( $route, $request ) );
	}

	public function testRouteMatchesReturnsTrueIfRouteParamMatches(): void
	{
		$route   = new Route( '/user/:id' );
		$request = new Request();
		$request->setPath( '/user/27' );
		$this->assertTrue( $this->matcher->routeMatches( $route, $request ) );

		$route   = new Route( '/user/:id/settings' );
		$request = new Request();
		$request->setPath( '/user/27/settings' );
		$this->assertTrue( $this->matcher->routeMatches( $route, $request ) );
	}

	public function testRouteMatchesReturnsTrueIfRouteContainsAsterisk(): void
	{
		$route   = new Route( '/user/*' );
		$request = new Request();
		$request->setPath( '/user/27' );
		$this->assertTrue( $this->matcher->routeMatches( $route, $request ) );

		$route   = new Route( '/user/*' );
		$request = new Request();
		$request->setPath( '/user/still/matches' );
		$this->assertTrue( $this->matcher->routeMatches( $route, $request ) );
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

		$this->assertTrue( $segment->isParam() );
		$this->assertEquals( 'some_value', $segment->getPath() );
		$this->assertIsArray( $segment->getTypes() );
		$this->assertCount( 1, $segment->getTypes() );
		$this->assertEquals( 'any', $segment->getTypes()[ 0 ] );
	}

	public function testGenerateUrlDataMapCorrectlyHandlesParametersWithTypes(): void
	{
		$parsedRoute = $this->matcher->generateUrlDataMap( [ ':some_value<int|bool>' ] );
		$segment     = $parsedRoute[ 0 ];

		$this->assertTrue( $segment->isParam() );
		$this->assertEquals( 'some_value', $segment->getPath() );
		$this->assertIsArray( $segment->getTypes() );
		$this->assertCount( 2, $segment->getTypes() );
		$this->assertEquals( 'int', $segment->getTypes()[ 0 ] );
		$this->assertEquals( 'bool', $segment->getTypes()[ 1 ] );
	}

	public function testGenerateUrlDataMapCorrectlyHandlesRegularRoute(): void
	{
		$parsedRoute = $this->matcher->generateUrlDataMap( [ 'home' ] );
		$segment     = $parsedRoute[ 0 ];

		$this->assertFalse( $segment->isParam() );
		$this->assertEquals( 'home', $segment->getPath() );
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

	public function testIsUrlParameterReturnsFalseIfStringEmpty(): void
	{
		$this->assertFalse( $this->matcher->isUrlParameter( '' ) );
	}
}
