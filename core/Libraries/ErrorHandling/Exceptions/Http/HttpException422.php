<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException422 extends AbstractHttpException {
	const CODE 	= 422;
	const TITLE = 'Unprocessable Entity';
}