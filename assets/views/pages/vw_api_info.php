
<div class="container-fluid">

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?=$base_url;?>">Application</a>
        </li>
        <li class="breadcrumb-item active">REST API</li>
    </ol>

    <h1>REST API endpoints</h1>

	<ol class="table-contents">
		<li>
			<a href="#previous-considerations">Previous considerations</a>
			<ol>
				<li>
					<a href="#guide-nomenclature">About the nomenclature followed by this guide</a>
				</li>
				<li>
					<a href="#error-handling">About the error handling and invalid requests</a>
				</li>		
				<li>
					<a href="#api-format">About the API format</a>
				</li>							
			</ol>
		</li>
		<li>
			<a href="#api-endpoints">API endpoints</a>
			<ol>
				<li>
					<a href="#get-cities">List all the cities</a>
				</li>
				<li>
					<a href="#get-city">Get a particular city</a>
				</li>		
				<li>
					<a href="#get-sunrise-sunset">Get sunrise/sunset information of a particular city</a>
				</li>	
				<li>
					<a href="#create-city">Create a new city</a>
				</li>
				<li>
					<a href="#edit-city">Edit a particular city</a>
				</li>	
				<li>
					<a href="#update-city">Update a particular city</a>
				</li>	
				<li>
					<a href="#delete-city">Delete a particular city</a>
				</li>																		
			</ol>			
		</li>
					
	</ol>

	<h2 id="previous-considerations">Previous considerations</h2>

    <h3 id="guide-nomenclature">About the nomenclature followed by this guide:</h3>

    <p>
		<strong>[i:id]</strong>: a clause between square brackets means an implicit parameter on the url route. The part before
		the colon indicates the type, and the part after the colon the name of the parameter. About the types of the parameters, 
		<strong>i</strong> means <em>integer</em>, 
		<strong>a</strong> means alfanumeric (<em>string</em>) and 
		<strong>d</strong> means <em>double</em> (decimal number).
		This route parameter type nomenclature is inherited from the usage of the
		<a href="http://altorouter.com/">Altorouter</a> library, and you can read more about the another types available
		<a href="http://altorouter.com/usage/mapping-routes.html">here</a>.
	</p>

	<p>
		<strong>(?parameter=something)</strong>: in a route description, the parameters between parentheses are always optional. 
		Thus, if not provided the system will use the default ones or not take them into account but the request will succeed. 

		In a route, the parameters after a question mark are passed into the query string, after the actual route. 

		On this documentation, a question mark before the type of a parameter also means an optional parameter. So,
		/cities<strong>(?page=[i:page])</strong> and <strong>?int page = 1 [default]</strong> both mean the same thing: an optional 
		parameter of the type string named page. The first nomenclature shows in which roure is used and that is passed into the query string, the second one that it takes the value 1 by default (if invalid or missing).
	</p>

    <h3 id="error-handling">About the error handling and invalid requests:</h3>
    
    </p>
    	Whenever an error occurs (invalid request, database error, logic error, etc), the error handler fires and a payload
    	is returned to the API consumer to give some insight about what was wrong.
    </p>

    <p>
    	This API aims to provide an extensive list of HTTP meaningful errors, coupling HTTP standard status codes to the logic of the
    	application. So, an explanation about the error will be returned in the response body and the status of the response will serve
    	as an error identifier.
    </p>

    <p>
    	For example, if the consumer request for a resource that doesn't exist, an <strong>HTTP 404</strong> error will
    	be raised (Not Found), or if the endpoint requires a JSON body and a malformed one is provided an error <strong>HTTP 400</strong>
    	will occur (Bad Request). A complete list of all the HTTP status codes can be found 
    	<a href="https://developer.mozilla.org/es/docs/Web/HTTP/Status">here</a>, 
    	but listing all the HTTP errors used by this application and in which context is done is out of the scope of this quick guide.
    </p>

    <p>
    	If the application is running in the production environment, only a short description will be shown to the user:
    </p>

	<figure class="code-block">
		<figcaption>Division by 0 in production</figcaption>
		<pre>
			<code class="language-json">
		{
		    "errors": [
		        {
		            "title": "Internal Server Error",
		            "message": "Something went wrong"
		        }
		    ]
		}	</code>
		</pre>
	</figure>

    <p>
    	Instead, if it is running on development or testing environments, a more useful explanation will be shown to the developer including contextual information to help to trace the error source:
    </p>

	<figure class="code-block">
		<figcaption>Division by 0 in development</figcaption>
		<pre>
			<code class="language-json">
		{
		    "errors": [
		        {
		            "type": "ErrorException",
		            "message": "Division by zero",
		            "file": "C:\\xampp\\htdocs\\vreasy\\src\\Controllers\\Api\\Cities.php",
		            "line": 51
		        }
		    ]
		}	</code>
		</pre>
	</figure>

	<h3 id="api-format">About the API format</h3>

	<p>
		This API consumes and serves data in <strong>UTF-8</strong> charset, so if the consumer uses another one there can be some errors.
		The inputs are consumed and the outputs are served in <strong>application/json</strong> format and the client will have to be
		configured accordingly.
	</p>

	<h2 id="api-endpoints">API endpoints</h2>

	<p>
		The following endpoints are available, with the parameters and for the HTTP method specified. If a request is provided to an 
		endpoint with the incorrect HTTP method, the API will return a response with a <strong>HTTP 405</strong> status code (Method Not Allowed).
	</p>

    <h3 id="get-cities">GET /api/cities(?page=[i:page])</h3>

    <p>
    	Gets a list of all the cities, paginating them to 10 items per page (default limit, can be changed into the application configuration).
    	For example, if there are 45 cities, this endpoint will display them by groups of 10 and you can navigate from sets of 10 to sets of 10 (1-10, 11-20, ..., 40-45), splitting the result into 5 pages and providing links to navigate from one to another.
    </p>

    <h4>Optional parameters</h4>

	<figure class="code-block">
		<figcaption>?page=[i:page]</figcaption>
		<pre>
			<code class="language-csharp">
				?int page = 1 [default]</code>
		</pre>
	</figure>

    <h4>Response</h4>

    <p>
    	Supposing there are 45 results (5 pages) and we ask for /cities?page=3:
    </p>

	<figure class="code-block">
		<figcaption>JSON body</figcaption>
		<pre>
			<code class="language-json">
		{
			"cities": [
				{
					"name": "Boston",
					"latitude": 42.3602534,
					"longitude": -71.0582912
				},
				...
			],
			"pages": {
				"first": "http://application-url/api/cities?page=1",
				"prev": "http://application-url/api/cities?page=2",
				"next": "http://application-url/api/cities?page=4",
				"last": "http://application-url/api/cities?page=5"
			}

		}	</code>
		</pre>
	</figure>

	<p>
		The array of cities contains 10 cities (pagination limit), and since we are in the page 3 there are provided the previous and the next pages, and also the first and the last. If there aren't cities to show, this simply will return an empty list. The response
		status code will always be <strong>HTTP 200</strong> (OK) unless there is a server error.
	</p>

    <h3 id="get-city">GET /api/cities/[i:id](?date=[a:date]&timezone=[a:timezone])</h3>

    <p>
    	Returns the data of a specified city, including the sunrise-sunset information.
    </p>

    <h4>Implicit parameters</h4>

	<figure class="code-block">
		<figcaption>[i:id]</figcaption>
		<pre>
			<code class="language-csharp">
				int id</code>
		</pre>
	</figure>

    <h4>Optional parameters</h4>

	<figure class="code-block">
		<figcaption>?date=[a:date]&timezone=[a:timezone]</figcaption>
		<pre>
			<code class="language-csharp">
				?string date = currentDate() 	[default]
				?string timezone = 'UTC' 		[default]</code>
		</pre>
	</figure>

	<h4>Response</h4>

	<p>
		If the city doesn't exist, a <strong>HTTP 404</strong> status code (Not Found) will be returned. If the city can be retrieved,
		will be appended to the response body and the response status will be <strong>HTTP 200</strong> (OK).
	</p>

	<figure class="code-block">
		<figcaption>City</figcaption>
		<pre>
			<code class="language-json">
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912,
			"sunriseSunset": {
				"sunrise": "9:08:04 AM",
				"sunset": "12:25:03 AM",
				"date": "2019-06-23",
				"valid": true				
			}
		}	</code>
		</pre>
	</figure>

	<p>
		The <em>valid</em> field is an additional information which can be useful to guess if the returned sunrise-sunset information is valid. If not, the field will be set to <em>false</em> and the sunriseSunset payload will change to explain what was wrong with the request:
	</p>

	<figure class="code-block">
		<figcaption>SunriseSunset error</figcaption>
		<pre>
			<code class="language-json">
		{
			"title": "Invalid Request",
			"message": "Either latitude or longitude parameters are missing or invalid",
			"date": "2019-06-23",
			"valid": false			
		}	</code>
		</pre>
	</figure>

	<p>
		The error nomenclature is inherited from the usage of the 
		<a href="https://sunrise-sunset.org/api">Sunrise Sunset API</a>. 
		More about this subject can be read on the section <strong>Status codes</strong> on that same page.
	</p>



    <h3 id="get-sunrise-sunset">GET /api/cities/[i:id]/sunrise-sunset(?date=[a:date]&timezone=[a:timezone])</h3>

    <p>
    	Returns the sunrise-sunset information of a specified city. The request is very similar to the previous one,
    	but the response excludes the city information (name and coordinates).
    </p>

    <h4>Implicit parameters</h4>

	<figure class="code-block">
		<figcaption>[i:id]</figcaption>
		<pre>
			<code class="language-csharp">
				int id</code>
		</pre>
	</figure>

    <h4>Optional parameters</h4>

	<figure class="code-block">
		<figcaption>?date=[a:date]&timezone=[a:timezone]</figcaption>
		<pre>
			<code class="language-csharp">
				?string date = currentDate() 	[default]
				?string timezone = 'UTC' 		[default]</code>
		</pre>
	</figure>

	<h4>Response</h4>

	<p>
		If the city doesn't exist, a <strong>HTTP 404</strong> status code (Not Found) will be returned. If the city can be retrieved,
		will be appended to the response body and the response status will be <strong>HTTP 200</strong> (OK).
	</p>

	<figure class="code-block">
		<figcaption>SunriseSunset</figcaption>
		<pre>
			<code class="language-json">
		{
			"sunrise": "9:08:04 AM",
			"sunset": "12:25:03 AM",
			"date": "2019-06-23",
			"valid": true				
		}	</code>
		</pre>
	</figure>

	<p>
		If the sunrise-sunset retrieving of information is not correct, the payload will change accordingly as shown before.
	</p>

    <h3 id="search-cities">POST /api/cities/search(?page=[i:page])</h3>

    <p>
		Displays a paginated list of cities matching a searching criteria of name and/or latitude and/or longitude.
    </p>

    <h4>Optional parameters</h4>

	<figure class="code-block">
		<figcaption>?page=[i:page]</figcaption>
		<pre>
			<code class="language-csharp">
				?int page = 1 [default]</code>
		</pre>
	</figure>

	<h4>Optional JSON POST parameters</h4>

	<figure class="code-block">
		<figcaption>JSON body</figcaption>
		<pre>
			<code class="language-json">
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912
		}	</code>
			<code class="language-csharp">
				?string name
				?float latitude
				?float longitude</code>
		</pre>
	</figure>

	<p>
		Each and every one of the JSON POST parameters are optional. So if they are missing or invalid, the request will be processed anyway without them. Note that if all the parameters are missing or invalid this will be identical to getting the list of cities 
		(/api/cities(?page=[i:page])).
	</p>

	<h4>Response</h4>

    <p>
    	Supposing there are 35 matching results (4 pages) and we don't specify a page (so page 1 by default):
    </p>

	<figure class="code-block">
		<figcaption>JSON body</figcaption>
		<pre>
			<code class="language-json">
		{
			"cities": [
				{
					"name": "Boston",
					"latitude": 42.3602534,
					"longitude": -71.0582912
				},
				...
			],
			"pages": {
				"next": "http://application-url/api/cities?page=2",
				"last": "http://application-url/api/cities?page=4"
			}

		}	</code>
		</pre>
	</figure>

	<p>
		This will be rare, because city names are constrained to be unique and the combination of latitude/longitude also has to be unique,
		so it won't be frequent to have cities matching a same search criteria and searches will show usually only one city or none.
		If there aren't cities to show, this simply will return an empty list. The response
		status code will always be <strong>HTTP 200</strong> (OK) unless there is a server error.
	</p>

    <h3 id="create-city">POST /api/cities</h3>

    <p>
		Creates a new city with the following parameters: name, latitude, longitude. All the parameters are mandatory.
    </p>

	<h4>Mandatory JSON POST parameters</h4>

	<figure class="code-block">
		<figcaption>JSON body</figcaption>
		<pre>
			<code class="language-json">
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912
		}	</code>
			<code class="language-csharp">
				string name
				float latitude
				float longitude</code>
		</pre>
	</figure>

	<h4>Response</h4>

	<figure class="code-block">
		<figcaption>City</figcaption>
		<pre>
			<code class="language-json">
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912,
		}	</code>
		</pre>
	</figure>

	<p>
		If the request is successful, the created city will be appended to the response which will return a 
		<strong>HTTP 201</strong> status code (Created). Also, a <strong>Location</strong> header will be provided 
		with a link to the new resource.
		If some of the parameters are missing or invalid, the API will respond with a <strong>HTTP 400</strong> error (Bad Request).
		There can also be a handful of errors if the request headers (content type, charset, etc) are missing or not accepted.
	</p>

	<h3 id="edit-city">PUT /api/cities/[i:id]</h3>

	<p>
		Edits an existing city (replaces the previous entity with a new entity). All the parameters are mandatory.
	</p>

    <h4>Implicit parameters</h4>

	<figure class="code-block">
		<figcaption>[i:id]</figcaption>
		<pre>
			<code class="language-csharp">
				int id</code>
		</pre>
	</figure>

	<h4>Mandatory JSON POST parameters</h4>

	<figure class="code-block">
		<figcaption>JSON body</figcaption>
		<pre>
			<code class="language-json">
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912
		}	</code>
			<code class="language-csharp">
				string name
				float latitude
				float longitude</code>
		</pre>
	</figure>

	<h4>Response</h4>

	<figure class="code-block">
		<figcaption>City</figcaption>
		<pre>
			<code class="language-json">
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912,
		}	</code>
		</pre>
	</figure>

	<p>
		If the city doesn't exist, a <strong>HTTP 404</strong> status code (Not Found) will be returned.
		If the request is successful, the modified city will be appended to the response which will return a 
		<strong>HTTP 200</strong> status code (OK). 
		If some of the parameters are missing or invalid, the API will respond with a <strong>HTTP 400</strong> error (Bad Request).
	</p>

	<h3 id="update-city">PATCH /api/cities/[i:id]</h3>

	<p>
		Updates an existing city (replaces one or more fields with new information). All the parameters are optional.
	</p>

    <h3>Implicit parameters</h3>

	<figure class="code-block">
		<figcaption>[i:id]</figcaption>
		<pre>
			<code class="language-csharp">
				int id</code>
		</pre>
	</figure>

	<h4>Optional JSON POST parameters</h4>

	<figure class="code-block">
		<figcaption>JSON body</figcaption>
		<pre>
			<code class="language-json">
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912
		}	</code>
			<code class="language-csharp">
				?string name
				?float latitude
				?float longitude</code>
		</pre>
	</figure>

	<h4>Response</h4>

	<figure class="code-block">
		<figcaption>City</figcaption>
		<pre>
			<code class="language-json">
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912,
		}	</code>
		</pre>
	</figure>

	<p>
		If the city doesn't exist, a <strong>HTTP 404</strong> status code (Not Found) will be returned.
		If the request is successful, the modified city will be appended to the response which will return a 
		<strong>HTTP 200</strong> status code (OK).
	</p>


	<h3 id="delete-city">DELETE /api/cities/[i:id]</h3>

	<p>
		Deletes a specified city.
	</p>

    <h4>Implicit parameters</h4>

	<figure class="code-block">
		<figcaption>[i:id]</figcaption>
		<pre>
			<code class="language-csharp">
				int id</code>
		</pre>
	</figure>

	<h4>Response</h4>

	<figure class="code-block">
		<figcaption></figcaption>
		<pre>
			<code class="language-json"></code>
		</pre>
	</figure>

	<p>
		If the city doesn't exist, a <strong>HTTP 404</strong> status code (Not Found) will be returned.
		If the request is successful, the response will return a <strong>HTTP 204 code</strong> (No Content) which has an empty body.
	</p>

</div><!-- /.container-fluid -->

