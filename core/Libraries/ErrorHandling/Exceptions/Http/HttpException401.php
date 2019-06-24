<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException401 extends AbstractHttpException {
	const CODE 	= 401;
	const TITLE = 'Unauthorized';
}