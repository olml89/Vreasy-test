<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


abstract class AbstractHttpException extends \Exception implements HttpExceptionInterface {


	//headers for redirections, locations, authentications...
	//previous to create a stack trace
	public function __construct(string $message, array $headers = [], \Throwable $previous = NULL) { 

		parent::__construct($message, $this::CODE, $previous);

		foreach($headers as $header) {
			header($header);
		}

	}


	public function getTitle() : string {
		return $this::TITLE;
	}


}