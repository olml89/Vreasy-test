<?php declare(strict_types = 1);
namespace System\Libraries\Routing\Route;


final class ControllerRoute extends AbstractValidRoute implements ValidRouteInterface {


	private $controller = '';
	private $method 	= '';


	public function __construct(string $controller, string $method, array $params, ?string $group) {
		parent::__construct($params, $group);
		$this->controller 	= $controller;		
		$this->method  		= $method;
	}


	public function isCallable() : bool {
		return FALSE;
	}


	public function getController() : string {
		return $this->controller;
	}


	public function getMethod() : string {
		return $this->method;
	}


}