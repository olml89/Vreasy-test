<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException400 extends AbstractHttpException {
	const CODE 	= 400;
	const TITLE = 'Bad Request';
}