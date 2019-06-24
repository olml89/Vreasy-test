<?php declare(strict_types = 1);
namespace Application\Entities\City;


final class CityFactory {


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