<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


final class HttpException502 extends AbstractHttpException {
	const CODE 	= 502;
	const TITLE = 'Bad Gateway';
}