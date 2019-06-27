
<div class="container-fluid">

    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Application</li>
    </ol>

    <h1>Vreasy PHP Programming Test</h1>

	<p>
		This application is a proof of concept for 
		<a href="https://web.vreasy.com">Vreasy</a> 
		that allows to retrieve the 
		sunrise/sunset time for cities of the United States using the 
		<a href="http://sunrise-sunset.org/api">Sunrise-Sunset API</a>. 
		The source code of this application is published on this 
		<a href="https://github.com/olml89/Vreasy-test">GitHub repository</a>.
	</p>

	<ol class="table-contents">
		<li>
			<a href="#requirements">General requirements</a>
		</li>
		<li>
			<a href="#use-cases">Use cases</a>
		</li>
		<li>
			<a href="#tools">Tools & libraries usage</a>
		</li>						
	</ol>

	<h2 id="requirements">General requirements</h2>

	<p>
		The application contains the necessary set of RESTful services implemented by its own 
		<a href="<?=$base_url;?>/rest-api">REST API</a>. 
		The database connection-related parameters are set as environment variables and loaded using the 
		<a href="https://github.com/vlucas/phpdotenv">phpdotenv</a> library. 
		Structured exception handling and logging is implemented throghout all the request lifetime cycle. 
		The application is 
		<a href="https://www.php-fig.org/psr/psr-3">PSR-3</a>, 
		<a href="https://www.php-fig.org/psr/psr-4">PSR-4</a> and 
		<a href="https://www.php-fig.org/psr/psr-11">PSR-11</a> compliant, but not 
		<a href="https://www.php-fig.org/psr/psr-7">PSR-7</a>, 
		<a href="https://www.php-fig.org/psr/psr-17">PSR-17</a> or 
		<a href="https://www.php-fig.org/psr/psr-18">PSR-18</a> 
		as it uses the 
		<a href="https://symfony.com/doc/current/components/http_foundation.html">Symfony HTTP Foundation</a> component 
		as the request/response ecosystem and 
		<a href="http://docs.guzzlephp.org/en/stable">GuzzleHTTP</a> 
		as the HTTP client.
	</p>

	<h2 id="use-cases">Use cases</h2>

	<p>
		The app exposes the following resources:
	</p>

	<figure class="code-block">
		<figcaption>City</figcaption>
		<pre>
			<code class="language-json">
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912
		}	</code>
		</pre>
	</figure>

	<p>
		The application permits getting the
		<a href="<?=$base_url;?>/cities">list of all the cities</a>, 
		paginated at 10 items per page, and access to each city display view where the user can look up the sunrise-sunset times for that city, edit its information or delete it from the collection of cities. When a city is deleted it still can be recovered until the page is refreshed, then the deletion will be permanent. Quick links to edit and delete cities can also be found on the general list of cities. On top of the page there's also a tool to search cities filtered
		by name and/or coordinates. Finally, there's a menu to input 
		<a href="<?=$base_url;?>/cities/new">new cities</a> into the database. 
		All three fields (name, latitude, longitude) are mandatory.
	</p>

	<figure class="code-block">
		<figcaption>SunriseSunset</figcaption>
		<pre>
			<code class="language-json">
		{
			"sunrise": "9:08:04 AM",
			"sunset": "12:25:03 AM",
			"date": "2019-06-23"
		}	</code>
		</pre>
	</figure>

	<p>
		Given a city, the application retrieves sunrise/sunset time. The date an the time zone can be parametrized, either using 
		query params 
		(<em>/cities/[i:id]?date=yyyy-mm-dd&timezone=XXXXX</em>)
		or the built-in web controls inside the city display view. If not provided or invalid, the current date and 
		<a href="https://time.is/es/UTC">UTC</a> time are used by default.
	</p>

	<h2 id="tools">Tools & libraries usage</h2>

	<div class="row">

		<div class="col-lg-3">
        	<div class="card mb-3">
            	<div class="card-header">
                	<i class="fas fa-network-wired"></i> Backend libraries
            	</div>
            	<div class="card-body">

            		<p>PHP framework:</p>
					<ul>
						<li>
							A customized, <strong>built-in from scratch</strong> PHP framework based on 
							<a href="https://www.php.net">PHP</a> 7.2.1
						</li>
					</ul>

					<p>The framework uses the components:</p>

					<ul>
						<li>
							<a href="https://github.com/vlucas/phpdotenv">phpdotenv</a> 3.4.0 (environment setting)
						</li>
						<li>
							<a href="https://github.com/Seldaek/monolog">Monolog</a> 1.24.0 (logging)
						</li>
						<li>
							<a href="https://filp.github.io/whoops">Whoops</a> 2.3.2 (error handling)
						</li>
						<li>
							<a href="http://php-di.org">php-di</a> 6.0.8 (DI container)
						</li>
						<li>
							<a href="http://altorouter.com">Altorouter</a> 1.2.0 (routing)
						</li>
						<li>
							<a href="http://docs.guzzlephp.org/en/stable">Guzzle</a> 6.3.3 (HTTP client)
						</li>
						<li>
							<a href="https://symfony.com/doc/current/components/http_foundation.html">Symfony HttpFoundation</a> 
							4.3.1 (request/response cycle)
						</li>
					</ul>

            	</div>
            </div>
		</div>

		<div class="col-lg-3">
        	<div class="card mb-3">
            	<div class="card-header">
                	<i class="fas fa-paint-brush"></i> Frontend libraries
            	</div>
            	<div class="card-body">
            		<p>Javascript libraries:</p>
					<ul>
						<li>
							<a href="https://jquery.com">jQuery</a> 3.4.1, 
							<a href="https://jqueryui.com/easing">jQuery UI easing</a> 1.4.1
						</li>
						<li>
							<a href="https://momentjs.com">moment.js</a> 2.24.0
						</li>
					</ul>

					<p>CSS/JS libraries and components:</p>
					<ul>
						<li>
							<a href="https://getbootstrap.com">Bootstrap</a> 4.3.1
						</li>
						<li>
							<a href="https://prismjs.com">PrismJS</a> 1.16.0
						</li>
						<li>
							<a href="http://www.daterangepicker.com">Date Range Picker</a> 3.0.5
						</li>
					</ul>
            	</div>
            </div>
		</div>

		<div class="col-lg-3">
        	<div class="card mb-3">
            	<div class="card-header">
                	<i class="fas fa-archway"></i> Technology environment
            	</div>
            	<div class="card-body">
					<ul>
						<li>
							<a href="https://httpd.apache.org">HTTP Apache server</a> 2.4.29
						</li>
						<li>
							<a href="https://mariadb.org">MariaDB</a> 10.1.30
						</li>
					</ul>
            	</div>
            </div>
		</div>

		<div class="col-lg-3">
        	<div class="card mb-3">
            	<div class="card-header">
                	<i class="fas fa-tools"></i> Tools
            	</div>
            	<div class="card-body">
            		<ul>
						<li>
							<a href="https://getcomposer.org">composer</a> 1.8.6
						</li>
						<li>
							<a href="https://www.phpmyadmin.net">phpMyAdmin</a> 4.7.4
						</li>
						<li>
							<a href="https://phpunit.de">PHPUnit</a> 8.2.3
						</li>
						<li>
							<a href="https://travis-ci.com">Travis CI</a>
						</li>
						<li>
							<a href="https://codecov.io">Codecov</a>
						</li>						
						<li>
							<a href="https://www.sourcetreeapp.com">Sourcetree</a> 2.4.7.0
							(Git 2.14.1)
						</li>
						<li>
							<a href="https://www.sublimetext.com/3">Sublime Text</a> 3126
						</li>
						<li>
							<a href="https://www.getpostman.com/downloads/canary">Postman</a> 7.3.0-canary03
						</li>
					</ul>
            	</div>
            </div>
		</div>

	</div>

</div><!-- /.container-fluid -->

