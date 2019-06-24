https://travis-ci.org/olml89/Vreasy-test.svg?branch=master

# Vreasy-test
Implementation of a test for Vreasy using a light custom PHP Framework.

This application is a proof of concept for https://web.vreasy.com that allows to retrieve the sunrise/sunset time for cities of the United States using the http://sunrise-sunset.org/api RESTful API.

## Requirements
- PHP 7.2.1+

## Deployment guide
Simply clone or download this repository and extract the files into your directory of choice. Then install the project dependencies using composer

```console
composer install
```
The application will automatically bootstrap the database, creating it if not exists and seeding it with some default values, but to be able to do that you will have to provide the database credentials in a .env file located at the root of the project. The file has to have the following values:
```json
ENVIRONMENT=(your_environment_of_choice)
DB_HOST=(your_mysql_server_address)
DB_USER=(your_mysql_user)
DB_PASSWORD=(your_mysql_password)
DB_NAME="vreasy_test"
DB_CHARSET="utf8"
```
The environment string is optional and can take up to three values: 'development', 'testing', 'production'. If the value is invalid or missing, the application will assume a development environment.

DB_HOST, DB_USER and DB_PASSWORD are the credentials to authenticate with your mysql server. The DB_NAME and the DB_CHARSET can be whichever you want, but if you want the application to bootstrap the database for you instead of having to do it manually you must provide the values by default shown here.

## General overview

The application contains the necessary set of RESTful services implemented by its own REST API. The database connection-related parameters are set as environment variables and loaded using the [phpdotenv](https://github.com/vlucas/phpdotenv) library. Structured exception handling and logging is implemented throghout all the request lifetime cycle. The application is [PSR-3](https://www.php-fig.org/psr/psr-3), [PSR-4](https://www.php-fig.org/psr/psr-4) and [PSR-11](https://www.php-fig.org/psr/psr-11) compliant, but not [PSR-7](https://www.php-fig.org/psr/psr-7), [PSR-17](https://www.php-fig.org/psr/psr-17) or [PSR-18](https://www.php-fig.org/psr/psr-18) as it uses the [Symfony HTTP Foundation](https://github.com/symfony/http-foundation) component as the request/response ecosystem and [GuzzleHTTP](https://github.com/guzzle/guzzle) as the HTTP client. 

## Brief user guide
The app exposes the following resources: 
```json
City:
		{
			"name": "Boston",
			"latitude": 42.3602534,
			"longitude": -71.0582912
		}	
```
The application permits getting the list of all the cities, paginated at 10 items per page, and access to each city display view where the user can look up the sunrise-sunset times for that city, edit its information or delete it from the collection of cities. When a city is deleted it still can be recovered until the page is refreshed, then the deletion will be permanent. Quick links to edit and delete cities can also be found on the general list of cities. On top of the page there's also a tool to search cities filtered by name and/or coordinates. Finally, there's a menu to input new cities into the database. All three fields (name, latitude, longitude) are mandatory. 
```json
SunriseSunset
		{
			"sunrise": "9:08:04 AM",
			"sunset": "12:25:03 AM",
			"date": "2019-06-23"
		}	
```
Given a city, the application retrieves sunrise/sunset time. The date an the time zone can be parametrized, either using query params (/cities/[i:id]?date=yyyy-mm-dd&timezone=XXXXX) or the built-in web controls inside the city display view. If not provided or invalid, the current date and UTC time are used by default. 

A more comprehensive list about the API endpoints implemented in the app can we found in **/rest-api** once installed.



