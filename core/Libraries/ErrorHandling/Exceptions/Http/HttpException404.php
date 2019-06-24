<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException404 extends AbstractHttpException {
	const CODE 	= 404;
	const TITLE = 'Not Found';
}