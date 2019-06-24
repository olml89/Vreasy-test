<?php declare(strict_types = 1);
namespace Application\Entities\City;


final class CityFactory {


	public function getInvalidCoordinates(float $latitude, float $longitude) : array {

		$invalidCoordinates = [];

		if($latitude < -90 || $latitude > 90) {
			$invalidCoordinates['latitude'] = 'must be between -90 and 90';
		}

		if($longitude < -180 || $longitude > 180) {
			$invalidCoordinates['longitude'] = 'must be between -180 and 180';
		}

		return $invalidCoordinates;

	}


	public function createFromInput(array $input) : CityModel {

		return new CityModel(
			$input['name'],
			$input['latitude'], 
			$input['longitude']
		);

	}


	public function createFromDatabaseRow(array $row) : CityModel {

		return new CityModel(
			$row['name'],
			(float)$row['latitude'],
			(float)$row['longitude'],
			(int)$row['id']
		);

	}


	public function createFromCity(CityModel $city) : CityModel {

		return new CityModel(
			$city->getName(),
			$city->getLatitude(),
			$city->getLongitude(),
			$city->getId()
		);

	}


}