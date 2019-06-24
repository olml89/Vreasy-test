<?php declare(strict_types = 1);
namespace Application\Entities\SunriseSunset\SunriseSunsetError;


final class SunriseSunsetUnknownErrorModel extends AbstractSunriseSunsetErrorModel {


	private const TITLE 	= 'Unknown Error';
	private const MESSAGE 	= 'The request could not be processed due to a server error. The request may succeed if you try again';


	public function __construct(string $date) {
		parent::__construct(self::TITLE, self::MESSAGE, $date);
	}
	

}