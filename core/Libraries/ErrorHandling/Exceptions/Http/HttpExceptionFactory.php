<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


class HttpExceptionFactory {


	public static function badRequest(string $message = 'Malformed request body') : HttpException400 {
		return new HttpException400($message);
	}


	public static function unauthorized() : HttpException401 {
		return new HttpException401('Credentials missing');
	}


	public static function forbidden(string $message = 'Credentials failed') : HttpException403 {
		return new HttpException403($message);
	}


	public static function notFound(string $message) : HttpException404 {
		return new HttpException404($message);
	}


	public static function pageNotFound(string $page) : HttpException404 {
		return self::notFound('Page '.$page.' not found');
	}


	public static function methodNotAllowed(array $allowedMethods) : HttpException405 {
		return new HttpException405('Methods allowed: '.implode(', ', $allowedMethods));
	}


	public static function invalidCharset(string $unsupportedCharset, array $acceptedCharsets) : HttpException406 {
		return new HttpException406('Not acceptable charset '.$unsupportedCharset.', accepted charsets: '.implode(', ', $acceptedCharsets));
	}


	public static function notAcceptable(string $accept, array $acceptedTypes) : HttpException406 {
		return new HttpException406('Not acceptable request types: '.$accept.', response types: '.implode(', ', $acceptedTypes));
	}


	public static function notAcceptableCharset(string $accept, string $unsupportedCharset, array $acceptedTypes) : HttpException406 {
		return new HttpException406('Not acceptable charset '.$unsupportedCharset.' for accepted request type '.$accept.', acceptable charset: '.$acceptedTypes[$accept]);
	}


	public static function missingAcceptCharset(string $accept, array $acceptedTypes) : HttpException406 {
		return new HttpException406('Missing charset for accepted request type '.$accept.', acceptable charset: '.$acceptedTypes[$accept]);
	}


	public static function conflict(string $message = 'Conflict') : HttpException409 {
		return new HttpException409($message);
	}


	public static function missingMediaType(array $supportedType) : HttpException415 {
		return new HttpException415('The request misses a correct type, supported type: '.key($supportedType));
	}


	public static function unsupportedMediaType(string $type, array $supportedType) : HttpException415 {
		return new HttpException415('Unsupported input media type: '.$type.', supported type: '.key($supportedType));
	}


	public static function unspecifiedMediaTypeCharset(string $type, array $supportedType) : HttpException415 {
		return new HttpException415('Unspecified charset for input media type '.$type.', supported charset: '.$supportedType[$type]);
	}


	public static function unsupportedMediaTypeCharset(string $type, string $unsupportedCharset, array $supportedType) : HttpException415 {
		return new HttpException415('Unsupported charset '.$unsupportedCharset.' for input media type '.$type.', supported charset: '.$supportedType[$type]);
	}


	public static function unprocessableEntity() : HttpException422 {
		return new HttpException422('The server understands the content type and the syntax of the request is correct, but the request entity is not valid');
	}


	public static function unprocessableEntityUnexpectedFields(array $unexpectedFields) : HttpException422 {
		return new HttpException422('Unexpected input fields: '.implode(', ', $unexpectedFields));
	}


	public static function unprocessableEntityErrorFields(array $errorFields) : HttpException422 {

		$errorFields = array_map(function(string $expectedType, string $errorField) : string {
				return $errorField.' ('.$expectedType.')';
			}, $errorFields, array_keys($errorFields)
		);

		return new HttpException422('Errors in the following input fields: '.implode(', ', $errorFields));

	}


	public static function unprocessableEntityInvalidValues(array $invalidValues) : HttpException422 {

		$invalidValues = array_map(function(string $correctValue, string $field) : string {
				return $field.' '.$correctValue;
			}, $invalidValues, array_keys($invalidValues)
		);

		return new HttpException422('Invalid values: '.implode(', ', $invalidValues));

	}


	public static function internalServerError(?\Throwable $exception = NULL) : HttpException500 {
		$message = empty($exception)? 'Something went wrong' : $exception->getMessage();
		return new HttpException500($message);
	}


	public static function failure(string $message) : HttpException500 {
		return self::internalServerError(new \Exception($message));
	}


	public static function badGateway(string $remoteServer) : HttpException502 {
		return new HttpException502('Something went wrong in the remote server '.$remoteServer);
	}


}