<?php declare(strict_types = 1);
namespace Application\Entities\SunriseSunset\SunriseSunsetError;


final class SunriseSunsetInvalidDateModel extends AbstractSunriseSunsetErrorModel {


	private const TITLE 	= 'Invalid Date';
	private const MESSAGE 	= 'Date parameter is missing or invalid';


	public function __construct(string $date) {
		parent::__construct(self::TITLE, self::MESSAGE, $date);
	}


}