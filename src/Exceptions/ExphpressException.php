<?php

namespace Crossview\Exphpress\Exceptions;

use \Exception;

class ExphpressException extends Exception
{
	/**
	 * ExphpressException constructor.
	 * @param string $message
	 * @param int $httpStatusCode
	 */
	public function __construct(string $message = '', int $httpStatusCode = 500)
	{
		parent::__construct($message, $httpStatusCode);
	}
}