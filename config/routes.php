<?php declare(strict_types = 1);


return [

	/*
		FRONT-END WEB APPLICATION ROUTES
	*/

	/*
		GET /
		displays the welcoming page
	*/
	['GET', '/', 'Web\Info#index'],

	/*
		GET /rest-api
		displays information about the REST API
	*/
	['GET', '/rest-api', 'Web\Info#printApiInfo'],

	/*
		GET /cities(/page-[i:page])
		displays a paginated list of cities

		params: ?int page = 1
	*/
	['GET', '/cities', 'Web\Cities#getCities'],
	['GET', '/cities/page-[i:page]', 'Web\Cities#getCities'],

	/*
		GET /cities/[i:id](?date=[a:date]&timezone=[a:timezone])
		displays the view of a city. You can pass date/timezone to calculate the sunrise and sunset times accordingly

		params: int id
		additional query params: ?string date = current, ?string timezone = UTC
	*/
	['GET', '/cities/[i:id]', 'Web\Cities#getCity'],  		

	/*
		POST /cities/[i:id]
		reloads the view of a city with new date/timezone

		params: int id
		json post params: ?string date = current, ?string timezone = UTC
	*/
	['POST', '/cities/[i:id]', 'Web\Cities#recalculateSunriseSunset'],

	/*
		POST /cities/search(/page-[i:page])
		displays a paginated list of cities matching a searching criteria of name and/or latitude and/or longitude

		params: ?int page = 1
		json post params: ?string name, ?float latitude, ?float longitude
	*/
	['POST', '/cities/search', 'Web\Cities#searchCities'],
	['POST', '/cities/search/page-[i:page]', 'Web\Cities#searchCities'],	//additional params: name, latitude, longitude

	/*
		GET /cities/new
		displays the panel to input new city information
	*/
	['GET', '/cities/new', 'Web\Cities#createCity'],

	/*
		GET /cities/[i:id]/edit
		displays the panel to input new city information

		params: int id
	*/
	['GET', '/cities/[i:id]/edit', 'Web\Cities#editCity'],


	/*
		REST API WEB APPLICATION ROUTES
	*/

	/*
		GET /api
		displays a the version of the application
	*/
	['GET', '/api', 'Api\EntryPoint#index'],

	/*
		GET /api/cities(?page=[i:page])
		displays a paginated list of cities and navigation links to go backward/forward

		additional query params: ?int page = 1
	*/
	['GET', '/api/cities', 'Api\Cities#getCities'],		

	/*
		GET /api/cities/[i:id](?date=[a:date]&timezone=[a:timezone])
		returns a city with the corresponding sunrise-sunset information

		params: int id
		additional query params: ?string date = current, ?string timezone = UTC
	*/
	['GET', '/api/cities/[i:id]', 'Api\Cities#getCity'],							

	/*
		GET /api/cities/[i:id]/sunrise-sunset(?date=[a:date]&timezone=[a:timezone])
		returns the sunrise-sunset information corresponding to a city 

		params: int id
		additional query params: ?string date = current, ?string timezone = UTC
	*/
	['GET', '/api/cities/[i:id]/sunrise-sunset', 'Api\Cities#getSunriseSunset'], 	

	/*
		POST /api/cities/search(?page=[i:page])
		displays a paginated list of cities matching a searching criteria of name and/or latitude and/or longitude

		additional query params: ?int page = 1
		json post params: ?string name, ?float latitude, ?float longitude
	*/
	['POST', '/api/cities/search', 'Api\Cities#searchCities'],			

	/*
		POST /api/cities
		creates a new city with the following parameters: name, latitude, longitude

		json post params: string name, float latitude, float longitude
	*/
	['POST', '/api/cities', 'Api\Cities#createCity'],		

	/*
		PUT /api/cities/[i:id]
		updates an existing city (replaces the previous entity with a new entity)

		params: int id
		json post params: string name, float latitude, float longitude
	*/	
	['PUT', '/api/cities/[i:id]', 'Api\Cities#editCity'],		

	/*
		PATCH /api/cities/[i:id]
		updates an existing city (replaces one or more fields with new information)

		params: int id
		json post params: ?string name, ?float latitude, ?float longitude
	*/	
	['PATCH', '/api/cities/[i:id]', 'Api\Cities#updateCity'],

	/*
		DELETE /api/cities/[i:id]
		deletes an existing city

		params: int id
	*/	
	['DELETE', '/api/cities/[i:id]', 'Api\Cities#deleteCity']	//delete an existing city

];