<?php

namespace Exceptions;

use Crossview\Exphpress\Exceptions\ExphpressException;
use PHPUnit\Framework\TestCase;

class ExphpressExceptionTest extends TestCase
{

	public function testCanBeCreated(): void
	{
		$this->assertInstanceOf( ExphpressException::class, new ExphpressException( '' ) );
	}

	public function testConstructorSetsMessage(): void
	{
		$e = new ExphpressException( 'Error found' );
		$this->assertEquals( 'Error found', $e->getMessage() );
	}

	public function testconstructorSetsDefaultMessage(): void
	{
		$e = new ExphpressException();
		$this->assertEquals( '', $e->getMessage() );
	}

	public function testConstructorSetsCode(): void
	{
		$e = new ExphpressException('', 413);
		$this->assertEquals(413, $e->getCode());
	}

	public function testConstructorSetsDefaultCode(): void
	{
		$e = new ExphpressException( '' );
		$this->assertEquals( 500, $e->getCode() );
	}
}
