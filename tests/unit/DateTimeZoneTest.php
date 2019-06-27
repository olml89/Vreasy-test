<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;

use Application\Libraries\DateTimeZone\DateTimeZoneFactory;


class DateTimeZoneTest extends TestCase {


	protected $dateTimezoneFactory 	= NULL; // \Application\Libraries\DateTimeZone\DateTimeZoneFactory


	protected function setUp() : void {
		$this->dateTimezoneFactory = new DateTimeZoneFactory;
	}


	public function testAssertMissingDatesAndTimeZonesAreReplacedByDefaultValues() : void {

		$defaultDate = date('Y-m-d', time());
		$defaultTimezone = 'UTC';

		$info = []; //missing

		$dateTimezone = $this->dateTimezoneFactory->createFromInput($info);

		$this->assertEquals($defaultDate, $dateTimezone->getDate());
		$this->assertEquals($defaultTimezone, $dateTimezone->getTimezone());

	}


	public function testAssertInvalidDateWithInvalidYearIsReplacedByDefaultValue() : void {

		$defaultDate = date('Y-m-d', time());

		$info = [
			'date'	=> '20236-11-10', 		//invalid
			'timezone' => 'Europe/Madrid' 
		];

		$dateTimezone = $this->dateTimezoneFactory->createFromInput($info);

		$this->assertEquals($defaultDate, $dateTimezone->getDate());

	}


	public function testAssertInvalidDateWithInvalidMonthIsReplacedByDefaultValue() : void {

		$defaultDate = date('Y-m-d', time());

		$info = [
			'date'	=> '2014-15-10', 		//invalid
			'timezone' => 'Europe/Madrid' 
		];

		$dateTimezone = $this->dateTimezoneFactory->createFromInput($info);

		$this->assertEquals($defaultDate, $dateTimezone->getDate());

	}


	public function testAssertInvalidDateWithInvalidDayIsReplacedByDefaultValue() : void {

		$defaultDate = date('Y-m-d', time());

		$info = [
			'date'	=> '2014-10-43', 		//invalid
			'timezone' => 'Europe/Madrid' 
		];

		$dateTimezone = $this->dateTimezoneFactory->createFromInput($info);

		$this->assertEquals($defaultDate, $dateTimezone->getDate());

	}


	public function testAssertInvalidTimeZoneIsReplacedByDefaultValue() : void {

		$defaultTimezone = 'UTC';

		$info = [
			'date'	=> '2014-03-30', 			
			'timezone' => 'Invalid/Timezone' 	//invalid
		];

		$dateTimezone = $this->dateTimezoneFactory->createFromInput($info);

		$this->assertEquals($defaultTimezone, $dateTimezone->getTimezone());

	}


}