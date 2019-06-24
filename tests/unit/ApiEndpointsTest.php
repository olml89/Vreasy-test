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
	public function testAssertNoExistingCityReturns404() : void {
		$invalidId = 0;
		$statusCode = $this->client->get($this->baseUrl.'/api/cities/'.$invalidId)->getStatusCode();
		$this->assertEquals(404, $statusCode);
	}


	//try to list cities, but with incorrect charset
	public function testAssertInvalidAcceptCharsetReturns406() : void {

		$headers = [
			'Accept-Charset' => 'INVALID_CHARSET',
			'Accept'		 => 'application/json',
			'Content-Type'	 => 'application/json'
		];

		$statusCode = $this->client->get($this->baseUrl.'/api/cities')->getStatusCode();
		$this->assertEquals(406, $statusCode);

	}

	//try to list cities, but with incorrect content-type


	//try to list cities, but with incorrect accept-type



	//try to create an existing city
	public function testAssertCantCreateAnExistingCityAndReturns409() : void {

		$city = [
			'name' => 'Boston',
			'latitude' => 42.3602534,
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