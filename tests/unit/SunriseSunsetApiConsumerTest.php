<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

use Application\Services\SunriseSunsetApiConsumer;
use Application\Entities\City\CityModel;


class SunriseSunsetApiConsumerTest extends TestCase {


	protected $apiConsumer	= NULL; // \Application\Services\SunriseSunsetApiConsumer


	protected function setUp() : void {

		$client = new Client([
            'user_agent'        => 'vreasy test',
            'http_errors'       => FALSE, 
            'allow_redirects'   => ['track_redirects' => TRUE]
        ]);

		$this->apiConsumer = new SunriseSunsetApiConsumer($client);

	}


	public function testAssertInvalidCityReturnsNull() : void {

		$date = date('Y-m-d', time());
		$city = new CityModel('Invalid', 0.0, 0.0);
		$info = $this->apiConsumer->getInfoByCity($city, $date);

		$this->assertNull($info);

	}


	public function testAssertValidCityReturnsArray() : void {

		$date = date('Y-m-d', time());
		$city = new CityModel('Boston', 42.3602534, -71.0582912);
		$info = $this->apiConsumer->getInfoByCity($city, $date);

		$this->assertIsArray($info);
		$this->assertTrue(array_key_exists('results', $info));

		$results = $info['results'];

		$this->assertTrue(array_key_exists('sunrise', $results));
		$this->assertTrue(array_key_exists('sunset', $results));

	}


}