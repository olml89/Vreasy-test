<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;

use Application\Entities\City\CityModel;
use Application\Entities\City\CityFactory;


class CityFactoryTest extends TestCase {


	protected $city 		= NULL; // \Application\Entities\City\CityModel
	protected $cityFactory 	= NULL; // \Application\Entities\City\CityFactory


	protected function setUp() : void {
		$this->city = new CityModel('Boston', 42.3602534, -71.0582912);
		$this->cityFactory = new CityFactory();
	}


	public function testAssertWeCanDetectInvalidCoordinates() : void {
		$invalidLatitude = -91.23;
		$validLongitude = 32.2233;
		$this->assertNotEmpty($this->cityFactory->getInvalidCoordinates($invalidLatitude, $validLongitude));
	}


	public function testAssertCreationFromInputIsCorrect() : void {

		$input = [
			'name' => 'Boston',
			'latitude' => 42.3602534,
			'longitude' => -71.0582912
		];

		$this->assertEquals($this->city, $this->cityFactory->createFromInput($input));

	}


	public function testAssertCreationFromAnotherCityIsCorrect() : void {
		$this->assertEquals($this->city, $this->cityFactory->createFromCity($this->city));
	}


}