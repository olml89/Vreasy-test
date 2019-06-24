<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException500 extends AbstractHttpException {
	const CODE 	= 500;
	const TITLE = 'Internal Server Error';
}