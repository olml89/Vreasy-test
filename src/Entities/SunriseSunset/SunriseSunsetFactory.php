<?php declare(strict_types = 1);
namespace Application\Entities\SunriseSunset;


use Application\Entities\SunriseSunset\SunriseSunsetError\SunriseSunsetUnknownErrorModel;
use Application\Entities\SunriseSunset\SunriseSunsetError\SunriseSunsetInvalidRequestModel;
use Application\Entities\SunriseSunset\SunriseSunsetError\SunriseSunsetInvalidDateModel;


final class SunriseSunsetFactory {


	public function createFromInfo(?array $sunriseSunsetInfo, string $date, string $timezone) : SunriseSunsetInterface {

		if(empty($sunriseSunsetInfo) || $sunriseSunsetInfo['status'] === 'UNKNOWN_ERROR') {
			return new SunriseSunsetUnknownErrorModel($date);
		}

		if($sunriseSunsetInfo['status'] === 'INVALID_REQUEST') {
			return new SunriseSunsetInvalidRequestModel($date);
		}

		if($sunriseSunsetInfo['status'] === 'INVALID_DATE') {
			return new SunriseSunsetInvalidDateModel($date);
		}

		//results retrieved by the API are always in UTC. so we set PHP time functions to UTC, we store the timestamp and set the timezone
		//to the timezone required. Thus when converting timestamps using date(), PHP will apply the difference
		date_default_timezone_set('UTC');
		$sunrise = strtotime($sunriseSunsetInfo['results']['sunrise']);
		$sunset = strtotime($sunriseSunsetInfo['results']['sunset']);
		date_default_timezone_set($timezone);

		// H:i:s format may be preferred, but the API returns results in g:i:s A format, so we will be coherent
		return new SunriseSunsetModel(
			date('g:i:s A', $sunrise), 		
			date('g:i:s A', $sunset),
			$date
		);

	}


}