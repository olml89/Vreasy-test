<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\HeaderBag as SymfonyHeaderBag;

use System\Libraries\Validation\RequestValidator;
use System\Libraries\ErrorHandling\Exceptions\Http\HttpException406 as HttpNotAcceptable406; 		
use System\Libraries\ErrorHandling\Exceptions\Http\HttpException415 as HttpUnsupportedMediaType415; 


class RequestValidationHeadersTest extends TestCase {


	protected $request 			= NULL; // \Symfony\Component\HttpFoundation\Request
	protected $requestValidator	= NULL; // \System\Libraries\Validation\RequestValidator


	protected function setUp() : void {

		//initialize a request
		$this->request = new SymfonyRequest();

		//initialize validator. Mock JSON API configuration
		$this->requestValidator = new RequestValidator();
		$this->requestValidator->setRequestContentType(['application/json' => 'utf-8'], TRUE); //test in strict mode: require Content-Type charset
		$this->requestValidator->setResponseAcceptTypes(['application/json' => 'utf-8']);

	}


	//incorrect Accept-Charset
	public function testAssertInvalidAcceptCharsetReturns406() : void {

		$this->expectException(HttpNotAcceptable406::class);

		$headers = [
			'Accept-Charset' 	=> 'INVALID_CHARSET',	//invalid
			'Accept'			=> 'application/json',
			'Content-Type'	 	=> 'application/json'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);

	}


	//POST request: requires a body, but Content-Type is not set
	public function testAssertVoidContentTypeOnPostRequestsReturns415() : void {

		$this->expectException(HttpUnsupportedMediaType415::class);

		$headers = []; //missing Content-Type

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->request->setMethod(SymfonyRequest::METHOD_POST);

		$this->requestValidator->validateHeaders($this->request);


	}


	//POST request: requires a body, Content-Type is set, but invalid
	public function testAssertInvalidContentTypeOnPostRequestsReturns415() : void {

		$this->expectException(HttpUnsupportedMediaType415::class);

		$headers = [
			'Content-Type' => 'INVALID'	//invalid
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->request->setMethod(SymfonyRequest::METHOD_POST);

		$this->requestValidator->validateHeaders($this->request);

	}


	//POST request: requires a body, Content-Type is set and valid, charset is required (strict mode), but not specified
	public function testAssertValidContentTypeWithVoidCharsetOnPostRequestsReturns415() : void {

		$this->expectException(HttpUnsupportedMediaType415::class);

		$headers = [
			'Content-Type' => 'application/json'	//invalid (misses charset)
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->request->setMethod(SymfonyRequest::METHOD_POST);

		$this->requestValidator->validateHeaders($this->request);

	}


	//POST request: requires a body, Content-Type is set and valid, charset is required (strict mode) and set, but invalid
	public function testAssertValidContentTypeWithInvalidCharsetOnPostRequestsReturns415() : void {

		$this->expectException(HttpUnsupportedMediaType415::class);

		$headers = [
			'Content-Type' => 'application/json; charset=INVALID'	//invalid (misses charset)
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->request->setMethod(SymfonyRequest::METHOD_POST);

		$this->requestValidator->validateHeaders($this->request);

	}


	//invalid Accept-Charset
	public function testAssertInvalidAcceptReturns406() : void {

		$this->expectException(HttpNotAcceptable406::class);

		$headers = [
			'Accept' => 'INVALID'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);

	}


	//valid Accept, but Accept-Charset not set, and implicit charset not set either
	public function testAssertVoidAcceptCharsetAndValidAcceptWithVoidImplicitCharsetReturns406() : void {

		$this->expectException(HttpNotAcceptable406::class);

		$headers = [
			'Accept' => 'application/json'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);

	}


	//valid Accept, but Accept-Charset not set, and implicit charset is invalid
	public function testAssertVoidAcceptCharsetAndValidAcceptWithInvalidImplicitCharsetReturns406() : void {

		$this->expectException(HttpNotAcceptable406::class);

		$headers = [
			'Accept' => 'application/json; charset=INVALID'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);

	}


	//valid Accept and implicit charset is invalid, but we set before a valid Accept-Charset
	public function testAssertValidAcceptCharsetAndValidAcceptWithInvalidImplicitCharsetIsASuccess() : void {

		$headers = [
			'Accept-Charset'	=> 'utf-8',
			'Accept' 			=> 'application/json; charset=INVALID'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);
		$this->assertTrue(TRUE);

	}


}