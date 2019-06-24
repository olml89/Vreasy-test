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
ENVIRONMENT=[your_environment]
DB_HOST=[your_mysql_server_address]
DB_USER=[your_mysql_user]
DB_PASSWORD=[your_mysql_password]
DB_NAME="vreasy_test"
DB_CHARSET="utf8"
```
The environment string is optional and can take up to three values: 'development', 'testing', 'production'. If the value is invalid or missing, the application will assume a development environment.

DB_HOST, DB_USER and DB_PASSWORD are the credentials to authenticate with your mysql server. The DB_NAME and the DB_CHARSET can be whichever you want, but if you want the application to bootstrap the database for you instead of having to do it manually you must provide the values by default shown here.



