<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException406 extends AbstractHttpException {
	const CODE 	= 406;
	const TITLE = 'Not Acceptable';
}