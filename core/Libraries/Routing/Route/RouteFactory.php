<?php declare(strict_types = 1);
namespace System\Libraries\Routing\Route;


use Symfony\Component\HttpFoundation\Request as SymfonyRequest;


final class RouteFactory {


	public function create(SymfonyRequest $request, array $routeData) : RouteInterface {

		if(array_key_exists('allowed_methods', $routeData)) {
			return new InvalidMethodRoute($routeData['allowed_methods']);
		}

		//prepend request to pass down to methods
		$params = ['request' => $request] + $routeData['params'];

		if(is_callable($routeData['target'])) {

			return new CallableRoute(
				$routeData['target'],
				$params,
				$routeData['group']
			);

		}

		list($controller, $method) = explode('#', $routeData['target']);
		return new ControllerRoute($controller, $method, $params, $routeData['group']);

	}


}