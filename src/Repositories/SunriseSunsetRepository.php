<?php declare(strict_types = 1);
namespace Application\Repositories;


use System\Libraries\Configuration\Configuration;

use Application\Services\SunriseSunsetApiConsumer;
use Application\Entities\SunriseSunset\SunriseSunsetFactory;
use Application\Entities\SunriseSunset\SunriseSunsetInterface;
use Application\Libraries\DateTimeZone\DateTimeZoneModel;
use Application\Entities\City\CityModel;


final class SunriseSunsetRepository {


	private $sunriseSunsetApiConsumer 	= NULL; // \Application\Services\SunriseSunsetApiConsumer;
	private $sunsetSunriseFactory 		= NULL; // \Application\Entities\SunriseSunset\SunriseSunsetFactory;


	public function __construct(SunriseSunsetApiConsumer $sunriseSunsetApiConsumer, SunriseSunsetFactory $sunriseSunsetFactory) {
		$this->sunriseSunsetApiConsumer = $sunriseSunsetApiConsumer;	
		$this->sunriseSunsetFactory 	= $sunriseSunsetFactory;
	}	


	public function getByCity(CityModel $city, DateTimeZoneModel $dateTimeZone) : SunriseSunsetInterface {

		$sunriseSunsetInfo = $this->sunriseSunsetApiConsumer->getInfoByCity($city, $dateTimeZone->getDate());

		return $this->sunriseSunsetFactory->createFromInfo(
			$sunriseSunsetInfo, 
			$dateTimeZone->getDate(), 
			$dateTimeZone->getTimezone()
		);

	}


}