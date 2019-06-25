<?php declare(strict_types = 1);
namespace Application\Controllers\Api;


use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\JsonResponse as SymfonyJsonResponse;

use System\Libraries\Validation\RequestValidator;
use System\Libraries\Pagination\Pagination;
use System\Libraries\ErrorHandling\Exceptions\Http\HttpExceptionFactory;

use Application\Libraries\DateTimeZone\DateTimeZoneFactory as DateTimeZone;
use Application\Entities\City\CityModel;
use Application\Entities\City\CityFactory;
use Application\Repositories\CityRepository;
use Application\Repositories\SunriseSunsetRepository;


final class Cities extends AbstractRestController {


	private $pagination 				= NULL; // \System\Libraries\Pagination\Pagination;
	private $dateTimeZone 				= NULL; // \System\Libraries\DateTimeZone\DateTimeZoneFactory
	private $cityFactory 				= NULL; // \Application\Entities\City\CityFactory
	private $cityRepository 			= NULL; // \Application\Repositories\CityRepository
	private $sunriseSunsetRepository 	= NULL; // \Application\Repositories\SunriseSunsetRepository


	public function __construct(RequestValidator $requestValidator, 
								Pagination $pagination,
								DateTimeZone $dateTimeZone,
								CityFactory $cityFactory, 
								CityRepository $cityRepository, 
								SunriseSunsetRepository $sunriseSunsetRepository
								) {

		parent::__construct($requestValidator);

		$this->pagination = $pagination;
		$this->dateTimeZone = $dateTimeZone;
		$this->cityFactory = $cityFactory;
		$this->cityRepository = $cityRepository;
		$this->sunriseSunsetRepository = $sunriseSunsetRepository;

	}


	// GET /api/cities(?page=[i:page])
	public function getCities(SymfonyRequest $request) : SymfonyJsonResponse {

		//filter input: optional page. If not specified will be set to 1, if invalid will be corrected by the
		//pagination class (1 if negative, max_page if out of bounds)
		$get = $this->requestValidator->assertFlexibleQueryParameters($request, [
			'page' => 'int'
		]);

		$page = !empty($get['page'])? $get['page'] : 1;
		$this->pagination->setCurrentPage($page);

		//get the cities and prepare the response
		$cities = $this->cityRepository->getList($this->pagination);
		$response = [];

		if(!empty($cities)) {

			$response['cities'] = $cities;
			$full_url = $request->getScheme().'://'.$request->getHttpHost().$request->getBaseUrl();
			$pages = $this->pagination->getNavigationLinks($full_url.'/api/cities', $request->query);

			if(!empty($pages)) {
				$response['pages'] = $pages;
			}

		}

		return new SymfonyJsonResponse($response, 200);

	}


	// GET /api/cities/[i:id](?date=[a:date]&timezone=[a:timezone])
	public function getCity(SymfonyRequest $request, int $id) : SymfonyJsonResponse {

		//validate the headers, since there is not input filtering which performs an implicit header validation
		$this->requestValidator->validateHeaders($request);

		//get city. Early escape if it doesn't exist
		$city = $this->cityRepository->getById($id);

		if(empty($city)) {
			throw HttpExceptionFactory::notFound('City '.$id.' could not be found');
		}

		//filter input: optional date/timezone. If not specified/invalid, date will be current date and timezone UTC
		$get = $this->requestValidator->assertFlexibleQueryParameters($request, [
			'date'	 	=> 'string',
			'timezone'	=> 'string'
		]);

		$dateTimeZone = $this->dateTimeZone->createFromInput($get);

		//get sunriseSunset and return the city with the sunriseSunset appended
		$sunriseSunset = $this->sunriseSunsetRepository->getByCity($city, $dateTimeZone);
		$city->setSunriseSunset($sunriseSunset);

		return new SymfonyJsonResponse($city, 200);		// HTTP 200 OK

	}


	// GET /api/cities/[i:id]/sunrise-sunset(?date=[a:date]&timezone=[a:timezone])
	public function getSunriseSunset(SymfonyRequest $request, int $cityId) : SymfonyJsonResponse {

		//validate the headers, since there is not input filtering which performs an implicit header validation
		$this->requestValidator->validateHeaders($request);

		//get city. Early escape if it doesn't exist
		$city = $this->cityRepository->getById($cityId);

		if(empty($city)) {
			throw HttpExceptionFactory::notFound('City '.$cityId.' could not be found');
		}

		//filter input: optional date/timezone. If not specified/invalid, date will be current date and timezone UTC
		$get = $this->requestValidator->assertFlexibleQueryParameters($request, [
			'date' 		=> 'string',
			'timezone'	=> 'string'
		]);

		$dateTimeZone = $this->dateTimeZone->createFromInput($get);

		//get sunriseSunset and return it
		$sunriseSunset = $this->sunriseSunsetRepository->getByCity($city, $dateTimeZone);

		return new SymfonyJsonResponse($sunriseSunset, 200);		// HTTP 200 OK	

	}


	// POST /api/cities/search(?page=[i:page]) {name, latitude, longitude}
	public function searchCities(SymfonyRequest $request) : SymfonyJsonResponse {

		//filter input: optional page. If not specified will be set to 1, if invalid will be corrected by the
		//pagination class (1 if negative, max_page if out of bounds)
		$get = $this->requestValidator->assertFlexibleQueryParameters($request, [
			'page' => 'int'
		]);

		$page = !empty($get['page'])? $get['page'] : 1;
		$this->pagination->setCurrentPage($page);

		//filter input: optional name/latitude/longitude. If all are empty, this is equivalent to getCities()
		$post = $this->requestValidator->assertFlexibleJsonInput($request, [
			'name' 		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		]);

		//get the matching cities and prepare the response
		$cities = $this->cityRepository->search($post, $this->pagination);
		$response = [];

		if(!empty($cities)) {

			$response['cities'] = $cities;
			$full_url = $request->getScheme().'://'.$request->getHttpHost().$request->getBaseUrl();
			$pages = $this->pagination->getNavigationLinks($full_url.'/api/cities', $request->query);

			if(!empty($pages)) {
				$response['pages'] = $pages;
			}
			
		}

		return new SymfonyJsonResponse($response, 200);

	}


	// POST /api/cities {name, latitude, longitude}
	public function createCity(SymfonyRequest $request) : SymfonyJsonResponse {

		//filter input: required name, latitude, longitude
		$post = $this->requestValidator->assertStrictJsonInput($request, [
			'name' 		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		]);

		//we are assured the input is well-formed and the fields are from the correct type, now we must assure
		//the values are coherent
		$invalidCoordinates = $this->cityFactory->getInvalidCoordinates($post['latitude'], $post['longitude']);

		if(!empty($invalidCoordinates)) {
			throw HttpExceptionFactory::unprocessableEntityInvalidValues($invalidCoordinates); 
		}

		//all is correct, create the city and try to save it
		$city = $this->cityFactory->createFromInput($post);

		if(!$this->cityRepository->save($city)) {
			throw HttpExceptionFactory::failure('City could not be created');
		}

		$resource_url = $request->getScheme().'://'.$request->getHttpHost().$request->getBaseUrl().'/api/cities/'.$city->getId();
		return new SymfonyJsonResponse($city, 201, ['Location' => $resource_url]);	// HTTP 201 CREATED

	}


	// PUT /api/cities/[i:id] {name, latitude, longitude}
	public function editCity(SymfonyRequest $request, int $id) : SymfonyJsonResponse {

		//validate the headers, since there is not input filtering which performs an implicit header validation
		$this->requestValidator->validateHeaders($request);

		//get city. Early escape if it doesn't exist
		$city = $this->cityRepository->getById($id);

		if(empty($city)) {
			throw HttpExceptionFactory::notFound('City '.$id.' could not be found');
		}

		//filter input: required name, latitude, longitude
		$post = $this->requestValidator->assertStrictJsonInput($request, [
			'name' 		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		]);

		//we are assured the input is well-formed and the fields are from the correct type, now we must assure
		//the values are coherent
		$invalidCoordinates = $this->cityFactory->getInvalidCoordinates($post['latitude'], $post['longitude']);

		if(!empty($invalidCoordinates)) {
			throw HttpExceptionFactory::unprocessableEntityInvalidValues($invalidCoordinates); 
		}

		//create a new city from the previous one and change the fields
		$editedCity = $this->cityFactory->createFromCity($city);
		$editedCity->setName($post['name']);
		$editedCity->setLatitude($post['latitude']);
		$editedCity->setLongitude($post['longitude']);

		//try to update only of not equal
		if($editedCity != $city && !$this->cityRepository->edit($editedCity)) {
			throw HttpExceptionFactory::failure('City '.$city->getId().' could not be edited');
		}

		return new SymfonyJsonResponse($editedCity, 200);  //HTTP 200 OK

	}


	// PATCH /api/cities/[i:id] {name/latitude/longitude}
	public function updateCity(SymfonyRequest $request, int $id) : SymfonyJsonResponse { 

		//validate the headers, since there is not input filtering which performs an implicit header validation
		$this->requestValidator->validateHeaders($request);

		//get city. Early escape if it doesn't exist
		$city = $this->cityRepository->getById($id);

		if(empty($city)) {
			throw HttpExceptionFactory::notFound('City '.$id.' could not be found');
		}

		//filter input: optional name/latitude/longitude
		$post = $this->requestValidator->assertFlexibleJsonInput($request, [
			'name' 		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		]);

		//we are assured the input is well-formed and the fields are from the correct type, now we must assure
		//the values are coherent
		$invalidCoordinates = $this->cityFactory->getInvalidCoordinates($post['latitude'], $post['longitude']);

		if(!empty($invalidCoordinates)) {
			throw HttpExceptionFactory::unprocessableEntityInvalidValues($invalidCoordinates); 
		}

		//create a new city from the previous one and change the fields if needed
		$editedCity = $this->cityFactory->createFromCity($city);

		if(array_key_exists('name', $post)) {
			$editedCity->setName($post['name']);
		}

		if(array_key_exists('latitude', $post)) {
			$editedCity->setLatitude($post['latitude']);
		}

		if(array_key_exists('longitude', $post)) {
			$editedCity->setLongitude($post['longitude']);
		}

		//try to update only of not equal
		if($editedCity != $city && !$this->cityRepository->edit($editedCity)) {
			throw HttpExceptionFactory::failure('City '.$city->getId().' could not be edited');
		}

		return new SymfonyJsonResponse($editedCity, 200);  //HTTP 200 OK

	}


	// DELETE /api/cities/[i:id]
	public function deleteCity(SymfonyRequest $request, int $id) : SymfonyJsonResponse { 

		//validate the headers, since there is not input filtering which performs an implicit header validation
		$this->requestValidator->validateHeaders($request);

		//get city. Early escape if it doesn't exist
		$city = $this->cityRepository->getById($id);

		if(empty($city)) {
			throw HttpExceptionFactory::notFound('City '.$id.' could not be found');
		}

		//try to delete
		if(!$this->cityRepository->delete($city)) {
			throw HttpExceptionFactory::failure('City '.$city->getId().' could not be deleted');
		}

		return new SymfonyJsonResponse(NULL, 204);		//HTTP 204 NO CONTENT

	}


}

