<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\HeaderBag as SymfonyHeaderBag;

use System\Libraries\Validation\RequestValidator;
use System\Libraries\ErrorHandling\Exceptions\Http\HttpException400 as HttpBadRequest400; 	
use System\Libraries\ErrorHandling\Exceptions\Http\HttpException422 as HttpUnprocessableEntity422;

use Application\Entities\City\CityFactory;
use Application\Entities\City\CityModel;


class RequestValidationJsonInputsTest extends TestCase {


	protected $validHeaders		= NULL;	// \Symfony\Component\HttpFoundation\HeaderBag
	protected $requestValidator	= NULL; // \System\Libraries\Validation\RequestValidator


	protected function setUp() : void {

		//valid headers
		$this->validHeaders = new SymfonyHeaderBag([
			'Accept-Charset' 	=> 'utf-8',
			'Content-Type'		=> 'application/json; charset=utf-8',
			'Content'			=> 'application/json'
		]);

		//initialize validator. Mock JSON API configuration
		$this->requestValidator = new RequestValidator();
		$this->requestValidator->setRequestContentType(['application/json' => 'utf-8'], TRUE); //test in strict mode: require Content-Type charset
		$this->requestValidator->setResponseAcceptTypes(['application/json' => 'utf-8']);

	}


	//strict JSON input: malformed body
	public function testInvalidStrictJsonInputReturns400() : void {

		$this->expectException(\System\Libraries\ErrorHandling\Exceptions\Http\HttpException400::class);

		$expectedFields = [
			'name'		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		];

		$json = NULL; //malformed

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			[], //$_SERVER
			$json
		);

		$request->headers = $this->validHeaders;
		$this->requestValidator->assertStrictJsonInput($request, $expectedFields);

	}


	//strict JSON input: missing fields
	public function testMissingFieldsFromStrictJsonInputReturns422() : void {

		$this->expectException(HttpUnprocessableEntity422::class);

		$expectedFields = [
			'name'		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		];

		$json = json_encode([
			//'name'				//missing
			'latitude' 	=> -34.56,
			'longitude' => 23.4
		]);

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			[], //$_SERVER
			$json
		);

		$request->headers = $this->validHeaders;
		$this->requestValidator->assertStrictJsonInput($request, $expectedFields);

	}


	//strict JSON input: unexpected extra fields
	public function testUnexpectedFieldsInStrictJsonInputReturns422() : void {

		$this->expectException(HttpUnprocessableEntity422::class);

		$expectedFields = [
			'name'		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		];

		$json = json_encode([
			'extra'		=> 'whatever', 	//invalid (extra)
			'name' 		=> 'whatever',		
			'latitude' 	=> -34.56,
			'longitude' => -23.34	
		]);

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			[], //$_SERVER
			$json
		);

		$request->headers = $this->validHeaders;
		$this->requestValidator->assertStrictJsonInput($request, $expectedFields);

	}


	//strict JSON input: invalid fields
	public function testInvalidFieldsFromStrictJsonInputReturns422() : void {

		$this->expectException(HttpUnprocessableEntity422::class);

		$expectedFields = [
			'name'		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		];

		$json = json_encode([
			'name' 		=> FALSE,		//invalid
			'latitude' 	=> -34.56,
			'longitude' => 'INVALID'	//invalid
		]);

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			[], //$_SERVER
			$json
		);

		$request->headers = $this->validHeaders;
		$this->requestValidator->assertStrictJsonInput($request, $expectedFields);

	}


	//strict JSON input: malformed body
	public function testInvalidFlexibleJsonInputReturns400() : void {

		$this->expectException(HttpBadRequest400::class);

		$expectedFields = [
			'name'		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		];

		$json = NULL; //malformed

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			[], //$_SERVER
			$json
		);

		$request->headers = $this->validHeaders;
		$this->requestValidator->assertFlexibleJsonInput($request, $expectedFields);

	}


	//strict JSON input: unexpected fields
	public function testUnexpectedFieldsInFlexibleJsonInputReturns422() : void {

		$this->expectException(HttpUnprocessableEntity422::class);

		$expectedFields = [
			'name'		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		];

		$json = json_encode([
			'extra'		=> 'whatever', 	//invalid (extra)
			'name' 		=> 'whatever',		
			'latitude' 	=> -34.56,
			'longitude' => -23.34	
		]);

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			[], //$_SERVER
			$json
		);

		$request->headers = $this->validHeaders;
		$this->requestValidator->assertFlexibleJsonInput($request, $expectedFields);

	}


	//test we create valid cities from a valid request
	public function testAssertWeCreateValidCitiesFromAValidRequest() : void {

		$expectedFields = [
			'name'		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		];

		$requestFields = [
			'name' 		=> 'whatever',		
			'latitude' 	=> -34.56,
			'longitude' => -23.34	
		];

		$json = json_encode($requestFields);

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			[], //$_SERVER
			$json
		);

		$request->headers = $this->validHeaders;
		$post = $this->requestValidator->assertStrictJsonInput($request, $expectedFields);
		$city = (new CityFactory())->createFromInput($post);

		$this->assertInstanceOf(CityModel::class, $city);
		$this->assertEquals($requestFields['name'], $city->getName());
		$this->assertEquals($requestFields['latitude'], $city->getLatitude());
		$this->assertEquals($requestFields['longitude'], $city->getLongitude());

	}


}