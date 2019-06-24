<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;

use Application\Entities\City\CityModel;


class CityTest extends TestCase {


	protected $city = NULL; // \Application\Entities\City\CityModel


	protected function setUp() : void {
		$this->city = new CityModel('Boston', 42.3602534, -71.0582912);
	}


	public function testWeCanGetName() : void {
		$this->assertEquals('Boston', $this->city->getName());
	}


	public function testWeCanGetLatitude() : void {
		$this->assertEquals(42.3602534, $this->city->getLatitude());
	}


	public function testWeCanGetLongitude() : void {
		$this->assertEquals(-71.0582912, $this->city->getLongitude());
	}


	public function testCorrectJsonEncoding() : void {

		$encoded = json_encode([
			'name' => 'Boston',
			'latitude' => 42.3602534,
			'longitude' => -71.0582912
		]);

		$this->assertEquals($encoded, json_encode($this->city));

	}


}