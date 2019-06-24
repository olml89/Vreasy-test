<?php declare(strict_types = 1);
namespace System\Libraries\Routing\Route;


final class InvalidMethodRoute implements RouteInterface {


	private $allowed_methods = [];


	public function __construct(array $allowed_methods) {
		$this->allowed_methods = $allowed_methods;
	}


	public function isValidMethod() : bool {
		return FALSE;
	}


	public function getAllowedMethods() : array {
		return $this->allowed_methods;
	}


}