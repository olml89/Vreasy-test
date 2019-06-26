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
	public function testInvalidAcceptCharsetReturns406() : void {

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
	public function testVoidContentTypeOnPostRequestsReturns415() : void {

		$this->expectException(HttpUnsupportedMediaType415::class);

		$headers = []; //missing Content-Type

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->request->setMethod(SymfonyRequest::METHOD_POST);

		$this->requestValidator->validateHeaders($this->request);


	}


	//POST request: requires a body, Content-Type is set, but invalid
	public function testInvalidContentTypeOnPostRequestsReturns415() : void {

		$this->expectException(HttpUnsupportedMediaType415::class);

		$headers = [
			'Content-Type' => 'INVALID'	//invalid
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->request->setMethod(SymfonyRequest::METHOD_POST);

		$this->requestValidator->validateHeaders($this->request);

	}


	//POST request: requires a body, Content-Type is set and valid, charset is required (strict mode), but not specified
	public function testValidContentTypeWithVoidCharsetOnPostRequestsReturns415() : void {

		$this->expectException(HttpUnsupportedMediaType415::class);

		$headers = [
			'Content-Type' => 'application/json'	//invalid (misses charset)
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->request->setMethod(SymfonyRequest::METHOD_POST);

		$this->requestValidator->validateHeaders($this->request);

	}


	//POST request: requires a body, Content-Type is set and valid, charset is required (strict mode) and set, but invalid
	public function testValidContentTypeWithInvalidCharsetOnPostRequestsReturns415() : void {

		$this->expectException(HttpUnsupportedMediaType415::class);

		$headers = [
			'Content-Type' => 'application/json; charset=INVALID'	//invalid 
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->request->setMethod(SymfonyRequest::METHOD_POST);

		$this->requestValidator->validateHeaders($this->request);

	}


	//invalid Accept-Charset
	public function testInvalidAcceptReturns406() : void {

		$this->expectException(HttpNotAcceptable406::class);

		$headers = [
			'Accept' => 'INVALID'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);

	}


	//valid Accept, but Accept-Charset not set, and implicit charset not set either
	public function testVoidAcceptCharsetAndValidAcceptWithVoidImplicitCharsetReturns406() : void {

		$this->expectException(HttpNotAcceptable406::class);

		$headers = [
			'Accept' => 'application/json'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);

	}


	//valid Accept, but Accept-Charset not set, and implicit charset is invalid
	public function testVoidAcceptCharsetAndValidAcceptWithInvalidImplicitCharsetReturns406() : void {

		$this->expectException(HttpNotAcceptable406::class);

		$headers = [
			'Accept' => 'application/json; charset=INVALID'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);

	}


	//valid Accept and implicit charset is invalid, but we set before a valid Accept-Charset
	public function testAssertValidAcceptCharsetAndValidAcceptWithInvalidImplicitCharsetAreASuccess() : void {

		$headers = [
			'Accept-Charset'	=> 'utf-8',
			'Accept' 			=> 'application/json; charset=INVALID'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);
		$this->assertTrue(TRUE);

	}


	//valid headers pass successfully on a GET request (this is in facte the same as the previous test)
	public function testAssertValidHeadersOnGetRequestAreASuccess() : void {

		$headers = [
			'Accept-Charset'	=> 'utf-8',
			'Accept' 			=> 'application/json; charset=INVALID'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->requestValidator->validateHeaders($this->request);
		$this->assertTrue(TRUE);

	}


	//valid headers pass successfully on a POST request
	public function testAssertValidHeadersOnAPostRequestAreASuccess() : void {

		$headers = [
			'Accept-Charset'	=> 'utf-8',
			'Accept' 			=> 'application/json',
			'Content-Type'		=> 'application/json; charset=utf-8'
		];

		$this->request->headers = new SymfonyHeaderBag($headers);
		$this->request->setMethod(SymfonyRequest::METHOD_POST);

		$this->requestValidator->validateHeaders($this->request);
		$this->assertTrue(TRUE);

	}


}