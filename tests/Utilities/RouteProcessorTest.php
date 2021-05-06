<?php

namespace Utilities;

use Crossview\Exphpress\Http\Request;
use Crossview\Exphpress\Routing\Route;
use Crossview\Exphpress\Routing\RouteSegmentData;
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

	public function testRouteMatchesReturnsFalseIfFinalSegmentNotMatches(): void
	{
		$route   = new Route( '/user/home' );
		$request = new Request();
		$request->setPath( '/user/not-home' );
		$this->assertFalse( $this->matcher->routeMatches( $route, $request ) );
	}

	// processDataTypes
	public function testProcessDataTypesReturnsTrueIfTypeIsAny(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'any' ] );
		$this->assertTrue( $this->matcher->processDataTypes( $data, 'something' ) );
		$this->assertEquals( 'any', $data->getType() );
		$this->assertEquals( 'something', $data->getValue() );
	}

	public function testProcessDataTypesReturnsTrueIfTypeIsBool(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertTrue( $this->matcher->processDataTypes( $data, 'true' ) );
	}

	public function testProcessDataTypesReturnsTrueIfTypeIsNumber(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'number' ] );
		$this->assertTrue( $this->matcher->processDataTypes( $data, '2187' ) );
	}

	public function testProcessDataTypesReturnsTrueIfTypeIsString(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'string' ] );
		$this->assertTrue( $this->matcher->processDataTypes( $data, 'something' ) );
	}

	public function testProcessDataTypesReturnsFalseIfTypeNotMatches(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertFalse( $this->matcher->processDataTypes( $data, 'not a bool' ) );
	}

	// processAny
	public function testProcessAnyReturnsFalseIfTypesNotContainsAny(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertFalse( $this->matcher->processAny( $data, 'true' ) );
	}

	public function testProcessAnyReturnsTrueIfTypeIsAny(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'any' ] );
		$this->assertTrue( $this->matcher->processAny( $data, 5477 ) );
		$this->assertEquals( 'any', $data->getType() );
		$this->assertIsNumeric( $data->getValue() );
		$this->assertEquals( 5477, $data->getValue() );
	}

	// processBool
	public function testProcessBoolReturnsFalseIfTypesNotContainsBool(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [
			'number',
			'string'
		] );
		$this->assertFalse( $this->matcher->processBool( $data, '' ) );
	}

	public function testProcessBoolCorrectlyHandlesTrueString(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertTrue( $this->matcher->processBool( $data, 'true' ) );
		$this->assertTrue( $data->getValue() );
		$this->assertEquals( 'bool', $data->getType() );
	}

	public function testProcessBoolCorrectlyHandlesOneString(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertTrue( $this->matcher->processBool( $data, '1' ) );
		$this->assertTrue( $data->getValue() );
		$this->assertEquals( 'bool', $data->getType() );
	}

	public function testProcessBoolCorrectlyHandlesYesString(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertTrue( $this->matcher->processBool( $data, 'yes' ) );
		$this->assertTrue( $data->getValue() );
		$this->assertEquals( 'bool', $data->getType() );
	}

	public function testProcessBoolCorrectlyHandlesFalseString(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertTrue( $this->matcher->processBool( $data, 'false' ) );
		$this->assertFalse( $data->getValue() );
		$this->assertEquals( 'bool', $data->getType() );
	}

	public function testProcessBoolCorrectlyHandlesZeroString(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertTrue( $this->matcher->processBool( $data, '0' ) );
		$this->assertFalse( $data->getValue() );
		$this->assertEquals( 'bool', $data->getType() );
	}

	public function testProcessBoolCorrectlyHandlesNoString(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertTrue( $this->matcher->processBool( $data, 'no' ) );
		$this->assertFalse( $data->getValue() );
		$this->assertEquals( 'bool', $data->getType() );
	}

	public function testProcessBoolReturnsFalseIfValueInvalid(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertFalse( $this->matcher->processBool( $data, 'not falsy' ) );
	}

	// processNumber
	public function testProcessNumberReturnsFalseIfTypesNotContainsNumber(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'bool' ] );
		$this->assertFalse( $this->matcher->processNumber( $data, '1' ) );
	}

	public function testProcessNumberCorrectlyHandlesZero(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'number' ] );
		$this->assertTrue( $this->matcher->processNumber( $data, '0' ) );
		$this->assertEquals( 'number', $data->getType() );
		$this->assertIsNumeric( $data->getValue() );
		$this->assertEquals( 0, $data->getValue() );
	}

	public function testProcessNumberReturnsTrueIfValueNumeric(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'number' ] );
		$this->assertTrue( $this->matcher->processNumber( $data, '1138' ) );
		$this->assertEquals( 'number', $data->getType() );
		$this->assertIsNumeric( $data->getValue() );
		$this->assertEquals( 1138, $data->getValue() );

		$this->assertTrue( $this->matcher->processNumber( $data, '1.75' ) );
		$this->assertIsNumeric( $data->getValue() );
		$this->assertEquals( 1.75, $data->getValue() );

		$this->assertTrue( $this->matcher->processNumber( $data, '2e7' ) );
		$this->assertIsNumeric( $data->getValue() );
		$this->assertEquals( 2e7, $data->getValue() );
	}

	public function testProcessNumberReturnsFalseIfValueNotNumeric(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'number' ] );
		$this->assertFalse( $this->matcher->processNumber( $data, 'f1138' ) );
		$this->assertFalse( $this->matcher->processNumber( $data, '0b1111' ) );
		$this->assertFalse( $this->matcher->processNumber( $data, '0x5' ) );
		$this->assertFalse( $this->matcher->processNumber( $data, 'true' ) );
		$this->assertFalse( $this->matcher->processNumber( $data, 'false' ) );
	}

	// processString
	public function testProcessStringReturnsFalseIfTypesNotContainsString(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'number' ] );
		$this->assertFalse( $this->matcher->processString( $data, 'string' ) );
	}

	public function testProcessStringReturnsTrueIfTypesContainsString(): void
	{
		$data = new RouteSegmentData();
		$data->setTypes( [ 'string' ] );
		$this->assertTrue( $this->matcher->processString( $data, 'a string' ) );
		$this->assertEquals( 'string', $data->getType() );
		$this->assertEquals( 'a string', $data->getValue() );
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
		$parsedRoute = $this->matcher->generateUrlDataMap( [ ':some_value<number|bool>' ] );
		$segment     = $parsedRoute[ 0 ];

		$this->assertTrue( $segment->isParam() );
		$this->assertEquals( 'some_value', $segment->getPath() );
		$this->assertIsArray( $segment->getTypes() );
		$this->assertCount( 2, $segment->getTypes() );
		$this->assertEquals( 'number', $segment->getTypes()[ 0 ] );
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
			'number',
			'string',
			'bool'
		] );
		$this->assertCount( 3, $types );
		$this->assertEquals( 'number', $types[ 0 ] );
		$this->assertEquals( 'string', $types[ 1 ] );
		$this->assertEquals( 'bool', $types[ 2 ] );
	}

	public function testValidateUrlParameterTypesThrowsIfTypeInvalid(): void
	{
		$this->expectErrorMessage( 'int is not a valid parameter data type. Valid types are bool, number, string' );
		$this->matcher->validateUrlParameterTypes( [
			'int',
			'string'
		] );
	}

	// parseUrlParameterTypes
	public function testParseUrlParameterTypesReturnsArrayOfTypesIfValid(): void
	{
		$types = $this->matcher->parseUrlParameterTypes( ':matched_route<number|string|bool>' );
		$this->assertCount( 3, $types );
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
