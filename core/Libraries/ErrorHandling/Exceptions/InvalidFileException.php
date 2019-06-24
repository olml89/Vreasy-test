<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions;


final class InvalidFileException extends \Exception {


	public static function unReadable(string $filePath) : InvalidFileException {
		throw new InvalidFileException($filePath.' does not exist and cannot be loaded');
	}


	public static function invalidConfigArray(string $filePath) : InvalidFileException {
		throw new InvalidFileException($filePath.' seems malformed (is not a valid configuration array) and cannot be loaded');
	}


}

