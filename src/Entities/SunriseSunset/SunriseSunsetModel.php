<?php declare(strict_types = 1);
namespace Application\Entities\SunriseSunset;


final class SunriseSunsetModel extends AbstractSunriseSunset {


	private $sunrise = '';
	private $sunset = '';


	public function __construct(string $sunrise, string $sunset, string $date) {
		parent::__construct($date);
		$this->valid = TRUE;
		$this->sunrise = $sunrise;
		$this->sunset = $sunset;
	}


	public function getSunrise() : string {
		return $this->sunrise;
	}


	public function getSunset() : string {
		return $this->sunset;
	}


	//visibility: sunrise, sunset, (inherited: date, valid)
	public function jsonSerialize() : array {
		return get_object_vars($this);
	}


}