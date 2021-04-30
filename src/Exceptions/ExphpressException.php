<?php

namespace Crossview\Exphpress\Exceptions;

use \Exception;

class ExphpressException extends Exception
{
	private int $errorCode;
	/**
	 * ExphpressException constructor.
	 * @param string $message
	 * @param int $errorCode
	 */
	public function __construct(string $message, int $errorCode = 500)
	{
		parent::__construct($message);
		$this->errorCode = $errorCode;
	}

	/**
	 * @return int The error code to return
	 */
	public function getErrorCode(): int
	{
		return $this->errorCode;
	}
}