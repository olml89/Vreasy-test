<?php declare(strict_types = 1);
namespace Application\Libraries\DateTimeZone;


final class DateTimeZoneModel {


	private const DEFAULT_TIMEZONE = 'UTC';

	private $date = '';
	private $timezone = '';


	public function __construct(array $parameters) {

		//if date is not specified or is not valid, default: current date
		$this->date = (!array_key_exists('date', $parameters) || !$this->isValidDate($parameters['date']))? 
						date('Y-m-d', time()) : $parameters['date'];

		//if timezone is not specified or is not valid, default: UTC
		$this->timezone = (!array_key_exists('timezone', $parameters) || !$this->isValidTimezone($parameters['timezone']))? 
						self::DEFAULT_TIMEZONE : $parameters['timezone'];

	}


	private function isValidDate(string $date) : bool {

		//https://stackoverflow.com/questions/14504913/verify-valid-date-using-phps-datetime-class
		//It seems DateTime::createFromFormat only raises errors if the specified year is invalid (like 20234). For invalid days/months it only
		//gives a warning, because it tries to correct invalid values like: 2014/12/32 => 2015/01/01. So we have to take that into account
		$dateTime = \DateTime::createFromFormat('Y-m-d', $date);

		if($dateTime === FALSE) {
			return FALSE;
		}

		return empty(\DateTime::getLastErrors()['warning_count']);

	}


	private function isValidTimezone(string $timezone) : bool {
		return in_array($timezone, \DateTimeZone::listIdentifiers());
	}


	public function getDate() : string {
		return $this->date;
	}


	public function getTimezone() : string {
		return $this->timezone;
	}


}