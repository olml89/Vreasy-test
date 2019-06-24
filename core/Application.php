<?php declare(strict_types = 1);
namespace System;


use DI\Container;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use System\Libraries\ErrorHandling\Exceptions\Http\HttpExceptionFactory;
use System\Libraries\ErrorHandling\Exceptions\Http\HttpExceptionInterface;
use System\Libraries\Routing\Route\ValidRouteInterface;


final class Application {


	const VERSION = '1.0.0';

	const DEVELOPMENT 	= 'development';
	const TESTING 		= 'testing';
	const PRODUCTION	= 'production';

	const EXIT_OK 		= 0;
	const EXIT_ERROR 	= 1;


	private $environment 	= '';
	private $container		= NULL;	// \DI\Container
	private $request 		= NULL; // \Psr\Http\Message\ServerRequestInterface - \GuzzleHttp\Psr7\ServerRequest


	public function __construct(Container $container) {
		$this->container = $container;
		$this->environment = $this->setEnvironment();
	}


	//try to use an ENV variable. if not exists or invalid, try to use the default environment specified by the developers
	//if not, assume production (don't show sensible information)
	private function setEnvironment() : string {

		$environment = getenv('ENVIRONMENT') ?? '';

		if($environment !== self::DEVELOPMENT && $environment !== self::TESTING && $environment !== self::PRODUCTION) {
			return defined('ENVIRONMENT')? ENVIRONMENT : self::PRODUCTION;
		}

		return $environment;

	}


	public function isEnvironment(string $environment) : bool {
		return $this->environment === $environment;
	}


	public function isRunningInConsole() : bool {
		return PHP_SAPI === 'cli';
	}


	public function getRequest() : SymfonyRequest {
		return $this->request;
	}


	public function run(SymfonyRequest $request) : SymfonyResponse {

		//save the request to be accessible in deeper levels through Application->getRequest()
		$this->request = $request;

		//start handling errors
		$this->container->get('errorHandler')->bootstrap($this)->registerHandlers();

		//find a matching route
		$route = $this->container->get('router')->getMatch($request);

		if(is_null($route)) {
			throw HttpExceptionFactory::pageNotFound(current_url());
		}

		if(!$route->isValidMethod()) {
			throw HttpExceptionFactory::methodNotAllowed($route->getAllowedMethods());
		}

		//authorization
		/*
		if($route->inGroup('login') && ...) {
			//not needed in this app
		}
		*/

		//get a response from the matched route. We pass the request as the first parameter
		return $this->getResponse($route)->prepare($request); 	//assumes UTF-8

	}


	private function getResponse(ValidRouteInterface $route) : SymfonyResponse {

		//closure defined in routes.php
		if($route->isCallable()) {
			return call_user_func_array($route->getHandler(), $route->getParams());
		}

		//route pointing to a controller method
		$controller = $this->container->get('\\Application\\Controllers\\'.$route->getController()); //Composer PSR-4 autoloading + PHP-DI
		return call_user_func_array([$controller, $route->getMethod()], $route->getParams());

	}


	public function stop() : void {
		$container->get('errorHandler')->unregisterHandlers();
		exit(self::EXIT_OK);
	}


}