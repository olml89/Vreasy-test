<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


interface HttpExceptionInterface {
	public function getCode();		//mixed
	public function getMessage();	//mixed
	public function getTitle() : string;
}