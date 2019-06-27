<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

use Application\Entities\SunriseSunset\SunriseSunsetFactory;
use Application\Entities\SunriseSunset\SunriseSunsetError\AbstractSunriseSunsetErrorModel;
use Application\Entities\SunriseSunset\SunriseSunsetModel;

use Application\Services\SunriseSunsetApiConsumer;
use Application\Entities\City\CityModel;
use Application\Libraries\DateTimeZone\DateTimeZoneFactory;


class SunriseSunsetApiRepositoryTest extends TestCase {


	protected $sunriseSunsetFactory = NULL; // \Application\Entities\SunriseSunset\SunriseSunsetFactory
	protected $apiConsumer			= NULL; // \Application\Services\SunriseSunsetApiConsumer
	protected $DateTimeZoneFactory 	= NULL; // \Application\Libraries\DateTimeZone\DateTimeZoneFactory

	protected function setUp() : void {

		$client = new Client([
            'user_agent'        => 'vreasy test',
            'http_errors'       => FALSE, 
            'allow_redirects'   => ['track_redirects' => TRUE]
        ]);

		$this->apiConsumer = new SunriseSunsetApiConsumer($client);
		$this->dateTimezoneFactory = new DateTimeZoneFactory;
		$this->sunriseSunsetFactory = new SunriseSunsetFactory;

	}


	public function testAssertInvalidCityCoordinatesReturnsInvalidSunriseSunset() : void {

		$dateTimezone = $this->dateTimezoneFactory->createFromDefaults();
		$city = new CityModel('INVALID', 0.0, 0.0);

		$sunriseSunsetInfo = $this->apiConsumer->getInfoByCity($city, $dateTimezone->getDate());
		$sunriseSunset = $this->sunriseSunsetFactory->createFromInfo($sunriseSunsetInfo, $dateTimezone->getDate(), $dateTimezone->getTimezone());

		$this->assertInstanceOf(AbstractSunriseSunsetErrorModel::class, $sunriseSunset);

	}


	public function testAssertValidCityCoordinatesReturnsValidSunriseSunset() : void {

		$dateTimezone = $this->dateTimezoneFactory->createFromDefaults();
		$city = new CityModel('Boston', 42.3602534, -71.0582912);

		$sunriseSunsetInfo = $this->apiConsumer->getInfoByCity($city, $dateTimezone->getDate());
		$sunriseSunset = $this->sunriseSunsetFactory->createFromInfo($sunriseSunsetInfo, $dateTimezone->getDate(), $dateTimezone->getTimezone());

		$this->assertInstanceOf(SunriseSunsetModel::class, $sunriseSunset);

	}


}