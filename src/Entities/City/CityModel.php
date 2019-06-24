<?php declare(strict_types = 1);
namespace Application\Entities\City;


use Application\Entities\SunriseSunset\SunriseSunsetInterface;


final class CityModel implements \JsonSerializable {


	private $id 		= 0;
	private $name 		= '';
	private $latitude 	= 0.0;
	private $longitude 	= 0.0;

	private $sunriseSunset = NULL; // \Application\Entities\SunriseSunset\SunriseSunsetInterface


	public function __construct(string $name, float $latitude, float $longitude, int $id = 0) {
		$this->id 			= $id;
		$this->name 		= $name;
		$this->latitude 	= $latitude;
		$this->longitude 	= $longitude;
	}	


	public function getId() : int {
		return $this->id;
	}


	public function setId(int $id) : void {
		$this->id = $id;
	}


	public function getName() : string {
		return $this->name;
	}


	public function setName(string $name) : void {
		$this->name = $name;
	}


	public function getLatitude() : float {
		return $this->latitude;
	}


	public function setLatitude(float $latitude) : void {
		$this->latitude = $latitude;
	}


	public function getLongitude() : float {
		return $this->longitude;
	}


	public function setLongitude(float $longitude) : void {
		$this->longitude = $longitude;
	}


	public function getSunriseSunset() : ?SunriseSunsetInterface {
		return $this->sunriseSunset;
	}


	public function setSunriseSunset(?SunriseSunsetInterface $sunriseSunset) : void {
		$this->sunriseSunset = $sunriseSunset;
	}


	public function jsonSerialize() : array {

		$jsonObject = ['name' => $this->name, 'latitude' => $this->latitude, 'longitude' => $this->longitude];

		if(!empty($this->sunriseSunset)) {
			$jsonObject['sunriseSunset'] = $this->sunriseSunset;
		}

		return $jsonObject;

	}


}