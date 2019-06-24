<?php declare(strict_types = 1);
namespace System\Libraries\Routing\Route;


final class CallableRoute extends AbstractValidRoute implements ValidRouteInterface {


	private $handler = NULL; // \Closure


	public function __construct(\Closure $handler, array $params, ?string $group) {
		parent::__construct($params, $group);
		$this->handler = $handler;		
	}


	public function isCallable() : bool {
		return TRUE;
	}


	public function getHandler() : \Closure {
		return $this->handler;
	}


}