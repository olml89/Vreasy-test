<?php declare(strict_types = 1);
namespace System\Controllers;


use System\Libraries\Validation\RequestValidator;


abstract class AbstractBaseController implements BaseControllerInterface {


	protected $requestValidator = NULL; // \System\Libraries\Validation\RequestValidator


	public function __construct(?RequestValidator $requestValidator) {
		$this->requestValidator = $requestValidator;
	}


	public function __call(string $name, array $args) : void {
		throw new \BadMethodCallException("Instance method ".get_class($this)."->$name() doesn't exist");
	}


	public static function __callStatic(string $name, array $args) : void {
		throw new \BadMethodCallException("Instance method ".get_class($this)."->$name() doesn't exist");
	}


}