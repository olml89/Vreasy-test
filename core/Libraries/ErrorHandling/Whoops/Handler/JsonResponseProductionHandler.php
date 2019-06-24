<?php declare(strict_types = 1);
namespace System\Libraries\ErrorHandling\Whoops\Handler;


use Whoops\Handler\Handler;
use Whoops\Handler\JsonResponseHandler;


final class JsonResponseProductionHandler extends JsonResponseHandler {


	//private in the parent class, and no getter method provided... ugly but it works
	private $_jsonApi = FALSE;


	public function handle() : int {

		$error = [
			'title' 	=> $this->getException()->getTitle(),
			'message' 	=> $this->getException()->getMessage()
		];

		$response = $this->_jsonApi? ['errors' => [$error]] : $error;
        echo json_encode($response, defined('JSON_PARTIAL_OUTPUT_ON_ERROR') ? JSON_PARTIAL_OUTPUT_ON_ERROR : 0);
        return Handler::QUIT;

	}


	//ugly, but has to be compatible...
	public function setJsonApi($jsonApi = FALSE) {
		$this->_jsonApi = (bool)$jsonApi;		//set our own property which we can access
		return parent::setJsonApi($jsonApi); 	//bubble up
	}


}