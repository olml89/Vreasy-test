<?php declare(strict_types = 1);
namespace Application\Libraries\DateTimeZone;


final class DateTimeZoneFactory {


	public function createFromInput(array $input) : DateTimeZoneModel {
		return new DateTimeZoneModel($input);
	}


	public function createFromDefaults() : DateTimeZoneModel {
		return new DateTimeZoneModel([]);
	}


}