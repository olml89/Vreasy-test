<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Exceptions\Http;


abstract class AbstractHttpException extends \Exception implements HttpExceptionInterface {


	public function __construct(string $message, array $headers = []) { //headers for redirections, locations, authentications...

		parent::__construct($message, $this::CODE);

		foreach($headers as $header) {
			header($header);
		}

	}


	public function getTitle() : string {
		return $this::TITLE;
	}


}