<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException405 extends AbstractHttpException {
	const CODE 	= 405;
	const TITLE = 'Method Not Allowed';
}