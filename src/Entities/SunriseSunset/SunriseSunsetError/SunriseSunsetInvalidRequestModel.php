<?php declare(strict_types = 1);
namespace Application\Entities\SunriseSunset\SunriseSunsetError;


final class SunriseSunsetInvalidRequestModel extends AbstractSunriseSunsetErrorModel {


	private const TITLE 	= 'Invalid Request';
	private const MESSAGE 	= 'Either latitude or longitude parameters are missing or invalid';


	public function __construct(string $date) {
		parent::__construct(self::TITLE, self::MESSAGE, $date);
	}


}