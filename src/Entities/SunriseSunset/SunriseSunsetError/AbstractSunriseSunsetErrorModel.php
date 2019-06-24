<?php declare(strict_types = 1);
namespace Application\Entities\SunriseSunset\SunriseSunsetError;


use Application\Entities\SunriseSunset\AbstractSunriseSunset;


abstract class AbstractSunriseSunsetErrorModel extends AbstractSunriseSunset {


	protected $title 	 = '';
	protected $message = '';


	public function __construct(string $title, string $message, string $date) {
		parent::__construct($date);
		$this->valid = FALSE;
		$this->title = $title;
		$this->message = $message;
	}


	public function getTitle() : string {
		return $this->title;
	}


	public function getMessage() : string {
		return $this->message;
	}


	//visibility: title, message, (inherited: date, valid)
	public function jsonSerialize() : array {
		return get_object_vars($this);
	}


}