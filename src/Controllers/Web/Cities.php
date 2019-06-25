<?php declare(strict_types = 1);
namespace Application\Controllers\Web;


use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

use System\Libraries\Configuration\Configuration;
use System\Libraries\Templating\TemplaterInterface;
use System\Libraries\Validation\RequestValidator;
use System\Libraries\Pagination\Pagination;
use System\Libraries\ErrorHandling\Exceptions\Http\HttpExceptionFactory;

use Application\Libraries\DateTimeZone\DateTimeZoneFactory as DateTimeZone;
use Application\Repositories\CityRepository;
use Application\Repositories\SunriseSunsetRepository;


final class Cities extends AbstractWebController {


	private $pagination 				= NULL; // \System\Libraries\Pagination\Pagination;
	private $cityRepository 			= NULL; // \Application\Repositories\CityRepository
	private $sunriseSunsetRepository 	= NULL; // \Application\Repositories\SunriseSunsetRepository


	public function __construct(Configuration $config, 
								TemplaterInterface $templater, 
								RequestValidator $requestValidator,
								Pagination $pagination,
								DateTimeZone $dateTimeZone,
								CityRepository $cityRepository, 
								SunriseSunsetRepository $sunriseSunsetRepository
								) {

		parent::__construct($config, $templater, $requestValidator);

		$this->pagination = $pagination;
		$this->dateTimeZone = $dateTimeZone;
		$this->cityRepository = $cityRepository;
		$this->sunriseSunsetRepository = $sunriseSunsetRepository;

	}


	// GET /cities(/page-[i:page])
	public function getCities(SymfonyRequest $request, int $page = 1) : SymfonyResponse {

		//set up the pagination
		$this->pagination->setCurrentPage($page);

		//get cities and navigation pages
		$cities = $this->cityRepository->getList($this->pagination);
		$pages = $this->pagination->getPages();

		//base url and sidebar
		$base_url = base_url();
		$sidebar = $this->getSidebar('cities');

		//display views
		$data = [
			'base_url'	=> $base_url,
			'sidebar'	=> $sidebar,
			'cities'	=> $cities,
			'pages'		=> $pages
		];

		$html = $this->templater->template('list-cities', $data);
		return SymfonyResponse::create($html);

	}


	// GET /cities/[i:id](?date=[a:date]&timezone=[a:timezone])
	public function getCity(SymfonyRequest $request, int $id) : SymfonyResponse {

		//resources: get city. Early escape if it doesn't exist
		$city = $this->cityRepository->getById($id);

		if(empty($city)) {
			throw HttpExceptionFactory::notFound('City '.$id.' could not be found');
		}

		//filter input: optional date/timezone. If not specified/invalid, date will be current date and timezone UTC
		$get = $this->requestValidator->assertFlexibleQueryParameters($request, [
			'date'		=> 'string',
			'timezone'	=> 'string'
		]);

		$dateTimeZone = $this->dateTimeZone->createFromInput($get);

		//get sunriseSunset and assign it to the city
		$sunriseSunset = $this->sunriseSunsetRepository->getByCity($city, $dateTimeZone);
		$city->setSunriseSunset($sunriseSunset);

		//get timezones
		$timezones = \DateTimeZone::listIdentifiers(); 

		//base url and sidebar
		$base_url = base_url();
		$sidebar = $this->getSidebar('cities');

		//display views
		$data = [
			'base_url'		=> $base_url,
			'sidebar'		=> $sidebar,
			'city'			=> $city,
			'timezones'		=> $timezones,
			'datetimezone'	=> $dateTimeZone
		];

		$html = $this->templater->template('view-city', $data);
		return SymfonyResponse::create($html);

	}


	// POST /cities/[i:id]
	public function recalculateSunriseSunset(SymfonyRequest $request, int $id) : SymfonyResponse {

		$redirectUrl = base_url().'/cities/'.$id;

		$post = $this->requestValidator->assertFlexibleFormInput($request, [
			'date'		=> 'string', 
			'timezone'	=> 'string'
		]);

		$queryParameters = urldecode(http_build_query($post));

		if(!empty($queryParameters)) {
			$redirectUrl .= '?'.$queryParameters;
		}

		return new SymfonyRedirectResponse($redirectUrl, 302);	// HTTP 302 FOUND

	}


	// POST /cities/search
	public function searchCities(SymfonyRequest $request, int $page = 1) : SymfonyResponse {

		//set up the pagination
		$this->pagination->setCurrentPage($page);

		//filter input: optional name/latitude/longitude. If all are empty, this is equivalent to getCities()
		$post = $this->requestValidator->assertFlexibleFormInput($request, [
			'name' 		=> 'string',
			'latitude' 	=> 'float',
			'longitude' => 'float'
		]);

		//get cities and navigation pages
		$cities = $this->cityRepository->search($post, $this->pagination);
		$pages = $this->pagination->getPages();

		//base url and sidebar
		$base_url = base_url();
		$sidebar = $this->getSidebar('cities');

		//display views
		$data = [
			'base_url'			=> $base_url,
			'search_criteria' 	=> $post,
			'sidebar'			=> $sidebar,
			'cities'			=> $cities,
			'pages'				=> $pages
		];

		$html = $this->templater->template('search-cities', $data);
		return SymfonyResponse::create($html);

	}


	// GET /cities/new
	public function createCity(SymfonyRequest $request) : SymfonyResponse {

		//base url and sidebar
		$base_url = base_url();
		$sidebar = $this->getSidebar('new-city');

		//display views
		$data = [
			'base_url'	=> $base_url,
			'sidebar'	=> $sidebar,
		];

		$html = $this->templater->template('add-city', $data);
		return SymfonyResponse::create($html);

	}


	// GET /cities/[i:id]/edit
	public function editCity(SymfonyRequest $request, int $id) : SymfonyResponse {

		//resources: get city. Early escape if it doesn't exist
		$city = $this->cityRepository->getById($id);

		if(empty($city)) {
			throw HttpExceptionFactory::notFound('City '.$id.' could not be found');
		}

		//base url and sidebar
		$base_url = base_url();
		$sidebar = $this->getSidebar('cities');

		//display views
		$data = [
			'base_url'	=> $base_url,
			'sidebar'	=> $sidebar,
			'city'		=> $city
		];

		$html = $this->templater->template('edit-city', $data);
		return SymfonyResponse::create($html);

	}


}