<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException415 extends AbstractHttpException {
	const CODE 	= 415;
	const TITLE = 'Unsupported Media Type';
}