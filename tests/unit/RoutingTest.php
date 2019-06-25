<?php declare(strict_types = 1);


use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use System\Libraries\Routing\Route\RouteFactory;
use System\Libraries\Routing\Route\InvalidMethodRoute;
use System\Libraries\Routing\Route\CallableRoute;
use System\Libraries\Routing\Route\ControllerRoute;
use System\Libraries\Routing\CustomAltoRouter;


class RoutingTest extends TestCase {


	protected $router	= NULL; // \System\Libraries\Routing\CustomAltoRouter


	protected function setUp() : void {

		$routes = [
			['GET', '/api/valid-controller-route', 'SomeController#someMethod'],
			['GET', '/api/valid-callable-route', function() : void { return; }]
		];

		$this->router = new CustomAltoRouter(new RouteFactory, $routes);

	}


	//unexisting route
	public function testAssertUnexistingRouteIsNull() : void {

		$server = [
			'REQUEST_URI' 		=> '/api/route/not/found',	//invalid
			'REQUEST_METHOD' 	=> 'GET'
		];

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			$server,
			NULL
		);

		$this->assertNull($this->router->getMatch($request));

	}


	//existing route, but for a different method
	public function testAssertWeCatchValidRoutesWithInvalidMethod() : void {

		$server = [
			'REQUEST_URI' 		=> '/api/valid-controller-route',	
			'REQUEST_METHOD' 	=> 'POST'	//invalid
		];

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			$server,
			NULL
		);

		$this->assertInstanceOf(InvalidMethodRoute::class, $this->router->getMatch($request));

	}


	//existing route with valid method, check is callable
	public function testAssertWeGetValidCallableRoute() : void {

		$server = [
			'REQUEST_URI' 		=> '/api/valid-callable-route',	
			'REQUEST_METHOD' 	=> 'GET'
		];

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			$server,
			NULL
		);

		$this->assertInstanceOf(CallableRoute::class, $this->router->getMatch($request));

	}


	//existing route with valid method, check is controller type and we get the correct controller and method names
	public function testAssertWeGetValidControllerRouteWithCorrectControllerAndMethodNames() : void {

		$server = [
			'REQUEST_URI' 		=> '/api/valid-controller-route',	
			'REQUEST_METHOD' 	=> 'GET'
		];

		$request = new SymfonyRequest(
			[], //$_GET
			[], //$_POST
			[],	//request attributes parsed from PATH_INFO
			[], //$_COOKIE
			[], //$_FILES
			$server,
			NULL
		);

		$route = $this->router->getMatch($request);
		$this->assertInstanceOf(ControllerRoute::class, $route);
		$this->assertEquals('SomeController', $route->getController());
		$this->assertEquals('someMethod', $route->getMethod());

	}


}