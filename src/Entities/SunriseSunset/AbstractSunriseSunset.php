<?php declare(strict_types = 1);
namespace Application\Entities\SunriseSunset;


abstract class AbstractSunriseSunset implements SunriseSunsetInterface {


	protected $date 	= '';
	protected $valid 	= FALSE;


	public function __construct(string $date) {
		$this->date = $date;
	}


	public function isValid() : bool {
		return $this->valid;
	}


	public function getDate() : string {
		return $this->date;
	}


}