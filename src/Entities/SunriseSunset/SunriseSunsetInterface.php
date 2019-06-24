<?php declare(strict_types = 1);
namespace Application\Entities\SunriseSunset;


interface SunriseSunsetInterface extends \JsonSerializable {
	//objects implementing SunriseSunsetResponseInterface must implement public function jsonSerialize() : array
	public function getDate() : string;
}
