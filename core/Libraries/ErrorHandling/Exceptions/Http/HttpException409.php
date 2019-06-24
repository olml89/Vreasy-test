<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException409 extends AbstractHttpException {
	const CODE 	= 409;
	const TITLE = 'Conflict';
}