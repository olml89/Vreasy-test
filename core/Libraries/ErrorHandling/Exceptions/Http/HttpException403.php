<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException403 extends AbstractHttpException {
	const CODE 	= 403;
	const TITLE = 'Forbidden';
}