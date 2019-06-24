<?php declare(strict_types = 1);
namespace Application\Services;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

use System\Services\ApiConsumer\ApiConsumerInterface;
use Application\Entities\City\CityModel;


final class SunriseSunsetApiConsumer implements ApiConsumerInterface {


	private const ENDPOINT 	= 'https://api.sunrise-sunset.org/json';
	private $client 		= NULL; 			// \GuzzleHttp\Client


	public function __construct(Client $client) {
		$this->client = $client;
	}


	public function get(string $request_uri) : ResponseInterface {
		return $this->client->get(self::ENDPOINT.$request_uri);
	}


	public function getInfoByCity(CityModel $city, string $date) : ?array {

		$uri = '?lat='.$city->getLatitude().'&lng='.$city->getLongitude().'&date='.$date;
		$response = $this->get($uri);

		if($response->getStatusCode() !== 200) {
			return NULL;
		}

		$body = (string)$response->getBody();
		return json_decode($body, TRUE) ?? NULL;

	}


}