<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;


class CityRepositoryTest extends TestCase {


	protected $client 	= NULL; // \GuzzleHttp\Client
	protected $baseUrl 	= 'http://localhost/vreasy-test/www';


	protected function setUp() : void {

		$this->client = new \GuzzleHttp\Client([
			'user_agent'        => 'testing_api',
			'http_errors'       => FALSE, 
			'allow_redirects'   => ['track_redirects' => TRUE]
		]);

	}


	//get a city that doesn't exist
	public function testAssertGetCityNoExistingCityReturns404() : void {

		$invalidId = 0;	//invalid

		$headers = [
			'Accept-Charset' 	=> 'UTF-8',
			'Accept'			=> 'application/json',
			'Content-Type'	 	=> 'application/json'
		];

		$statusCode = $this->client->get($this->baseUrl.'/api/cities/'.$invalidId, ['headers' => $headers])->getStatusCode();
		$this->assertEquals(404, $statusCode);

	}


	//try to list cities, but with incorrect Accept-Charset
	public function testAssertGetCitiesInvalidAcceptCharsetReturns406() : void {

		$headers = [
			'Accept-Charset' 	=> 'INVALID_CHARSET',	//invalid
			'Accept'			=> 'application/json',
			'Content-Type'	 	=> 'application/json'
		];

		$statusCode = $this->client->get($this->baseUrl.'/api/cities', ['headers' => $headers])->getStatusCode();
		$this->assertEquals(406, $statusCode);

	}


	//try to list cities, but with Accept-Charset not set, and incorrect charset implicit on the Accept
	public function testAssertGetCitiesInvalidCharsetImplicitOnAcceptReturns406() : void {

		$headers = [
			'Accept-Charset' 	=> NULL,
			'Accept'			=> 'application/json;charset=INVALID',	//invalid
			'Content-Type'	 	=> 'application/json'
		];

		$statusCode = $this->client->get($this->baseUrl.'/api/cities', ['headers' => $headers])->getStatusCode();
		$this->assertEquals(406, $statusCode);

	}


	//try to list cities, but with Accept-Charset not set, and CORRECT charset implicit on the Accept
	public function testAssertGetCitiesValidCharsetImplicitOnAcceptReturns200() : void {

		$headers = [
			'Accept-Charset' 	=> NULL,
			'Accept'			=> 'application/json;charset=UTF-8',
			'Content-Type'	 	=> 'application/json'
		];

		$statusCode = $this->client->get($this->baseUrl.'/api/cities', ['headers' => $headers])->getStatusCode();
		$this->assertEquals(200, $statusCode);

	}


	//try to access and endpoint which requires content with a missing content-type
	public function testAssertMissingContentTypeOnEndpointRequiringContentReturns415() : void {

		$city = [
			'name' 		=> 'Boston',
			'latitude' 	=> 42.3602534,
			'longitude' => -71.0582912
		];

		$headers = [
			'Accept-Charset' => 'UTF-8',
			'Accept'		 => 'application/json',
			'Content-Type'   => NULL 		//invalid (missing) 
		];

		$response = $this->client->post($this->baseUrl.'/api/cities', ['headers' => $headers, 'json' => $city]);
		$this->assertEquals(415, $response->getStatusCode());

	}


	//try to access and endpoint which requires content with an invalid content-type
	public function testAssertInvalidContentTypeOnEndpointRequiringContentReturns415() : void {

		$city = [
			'name' 		=> 'Boston',
			'latitude' 	=> 42.3602534,
			'longitude' => -71.0582912
		];

		$headers = [
			'Accept-Charset' => 'UTF-8',
			'Accept'		 => 'application/json',
			'Content-Type'	 => 'INVALID'		//invalid
		];

		$response = $this->client->post($this->baseUrl.'/api/cities', ['headers' => $headers, 'json' => $city]);
		$this->assertEquals(415, $response->getStatusCode());

	}


	//try to access an endpoint with a missing accept type: API should interpret that we accept anything and return OK (200)
	public function testAssertMissingAcceptTypeIsAcceptedAsWeAcceptEverythingAndReturns200() : void {

		$headers = [
			'Accept-Charset' => 'UTF-8',
			'Accept'		 => NULL, //everything fine
			'Content-Type'	 => 'application/json'		
		];

		$response = $this->client->get($this->baseUrl.'/api/cities', ['headers' => $headers]);
		$this->assertEquals(200, $response->getStatusCode());

	}


	//try to access an endpoint with an invalid accept type: different story, we must receive a 406 error
	public function testAssertInvalidAcceptTypeIsRejectedAndReturns406() : void {

		$headers = [
			'Accept-Charset' => 'UTF-8',
			'Accept'		 => 'INVALID', //invalid
			'Content-Type'	 => 'application/json'		
		];

		$response = $this->client->get($this->baseUrl.'/api/cities', ['headers' => $headers]);
		$this->assertEquals(406, $response->getStatusCode());

	}


	//try to create an city with all the headers correct, but the request is malformed (null)
	public function testAssertCantCreateACityWithAMalformedJSON() : void {

		$city = NULL; //invalid (malformed)

		$headers = [
			'Accept-Charset' => 'UTF-8',
			'Accept'		 => 'application/json',	
			'Content-Type'	 => 'application/json'
		];

		$response = $this->client->post($this->baseUrl.'/api/cities', ['headers' => $headers, 'json' => $city]);
		$this->assertEquals(400, $response->getStatusCode());

	}


	//try to create an city with all the headers correct, but the name is missing
	public function testAssertCantCreateACityWithoutName() : void {

		$city = [							
			//'name'  	=> 'Boston'			//invalid (missing)
			'latitude' 	=> 42.3602534,
			'longitude' => -71.0582912
		];

		$headers = [
			'Accept-Charset' => 'UTF-8',
			'Accept'		 => 'application/json',	
			'Content-Type'	 => 'application/json'
		];

		$response = $this->client->post($this->baseUrl.'/api/cities', ['headers' => $headers, 'json' => $city]);
		$this->assertEquals(400, $response->getStatusCode());

	}


	//try to create an city with all the headers correct, but the name has not a correct type
	public function testAssertCantCreateACityWithIncorrectParameters() : void {

		$city = [							
			'name'  	=> FALSE,			//invalid 
			'latitude' 	=> 42.3602534,
			'longitude' => -71.0582912
		];

		$headers = [
			'Accept-Charset' => 'UTF-8',
			'Accept'		 => 'application/json',	
			'Content-Type'	 => 'application/json'
		];

		$response = $this->client->post($this->baseUrl.'/api/cities', ['headers' => $headers, 'json' => $city]);
		$this->assertEquals(400, $response->getStatusCode());

	}


	//try to create an city with all the headers correct and the parameters sintactically correct, but invalid (bad coordinates)
	public function testAssertCantCreateACityWithSyntacticallyCorrectButInvalidParameters() : void {

		$city = [							
			'name'  	=> 'Boston',			
			'latitude' 	=> 422.3602534,		//invalid (>90)
			'longitude' => -7123.05912		//invalid (<180)
		];

		$headers = [
			'Accept-Charset' => 'UTF-8',
			'Accept'		 => 'application/json',	
			'Content-Type'	 => 'application/json'
		];

		$response = $this->client->post($this->baseUrl.'/api/cities', ['headers' => $headers, 'json' => $city]);
		$this->assertEquals(400, $response->getStatusCode());

	}


	//try to create a city with all the headers and parameters correct, but already exists on the database (409 conflict, duplicated values)
	public function testAssertCantCreateAnExistingCityAndReturns409() : void {

		$city = [						//invalid, as it exists
			'name' 		=> 'Boston',
			'latitude' 	=> 42.3602534,
			'longitude' => -71.0582912
		];

		$headers = [
			'Accept-Charset' => 'UTF-8',
			'Accept'		 => 'application/json',	
			'Content-Type'	 => 'application/json'
		];

		$response = $this->client->post($this->baseUrl.'/api/cities', ['headers' => $headers, 'json' => $city]);
		$this->assertEquals(409, $response->getStatusCode());

	}


}